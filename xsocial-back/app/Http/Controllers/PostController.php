<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\ContentType;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\ImageKitService;


class PostController extends Controller
{
    public function makeUserPost(Request $request)
    {
        try {
            // Validación de entrada
            $request->validate([
                'content' => 'nullable|string|max:280',
                'gif' => 'nullable|url',
                'media' => 'nullable|file|mimes:jpeg,png,webp,gif|max:10240',
                'type' => 'nullable|string|in:photo,gif',
            ]);

            // Obtener usuario autenticado
            $user = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'No autenticado'], 401);
            }

            // Crear post
            $post = $user->post()->create(['content' => $request->content]);

            // Manejar GIF vía URL
            if ($request->has('gif') && $request->gif) {
                $contentType = ContentType::where('name', 'gif')->firstOrFail();
                $post->mediaPosts()->create([
                    'file_url' => $request->gif,
                    'content_type' => $contentType->id,
                ]);
            }

            // Manejar imagen subida
            elseif ($request->hasFile('media')) {
                $file = $request->file('media');
                Log::info('Archivo recibido:', [
                    'path' => $file->getRealPath(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'name' => $file->getClientOriginalName(),
                ]);

                // Subir archivo a ImageKit
                $imageKit = new ImageKitService();
                $uploadResponse = $imageKit->upload($file->getRealPath(), $file->getClientOriginalName());

                if (!$uploadResponse || isset($uploadResponse->error)) {
                    Log::error('Error en upload a ImageKit', ['error' => $uploadResponse->error ?? 'unknown']);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error al subir la imagen a ImageKit',
                    ], 500);
                }

                $url = $uploadResponse->result->url;
                Log::info('Upload exitoso a ImageKit', ['url' => $url]);

                // Determinar tipo de contenido automáticamente
                $mime = $file->getMimeType();
                $contentTypeName = in_array($mime, ['image/jpeg', 'image/png', 'image/webp']) ? 'photo' : 'gif';
                $contentType = ContentType::where('name', $contentTypeName)->firstOrFail();

                // Guardar en DB
                $post->mediaPosts()->create([
                    'file_url' => $url,
                    'content_type' => $contentType->id,
                ]);
            }

            // Respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Post publicado exitosamente!',
                'data' => PostResource::make($post->fresh()->load('mediaPosts')),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear el post: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el post: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function uploadTest()
    {
        try {
            $filePath = public_path('test.jpg'); // Imagen en carpeta /public
            $fileName = 'test.jpg';

            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El archivo test.jpg no existe en /public'
                ], 404);
            }


            $imageKit = new ImageKitService();
            $uploadResponse = $imageKit->upload($filePath, $fileName);

            if (!$uploadResponse || isset($uploadResponse->error)) {
                Log::error('Error en upload a ImageKit', ['error' => $uploadResponse->error ?? 'unknown']);
                return response()->json([
                    'status' => 'error',
                    'message' => $uploadResponse->error->message ?? 'Error desconocido al subir a ImageKit'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'url' => $uploadResponse->result->url,
                'name' => $uploadResponse->result->name,
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Excepción al subir a ImageKit: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
