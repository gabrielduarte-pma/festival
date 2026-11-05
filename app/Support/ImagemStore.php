<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagemStore
{
    /**
     * Salva uma imagem em storage/app/public/{pasta}, redimensionando (lado maior = $max)
     * e convertendo para WebP (mantém transparência). Devolve o caminho relativo ao disco "public".
     */
    public static function salvar(UploadedFile $arquivo, string $pasta, int $max = 1600): string
    {
        $nome = Str::random(24);

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $img = @imagecreatefromstring(file_get_contents($arquivo->getRealPath()));
            if ($img !== false) {
                $w = imagesx($img);
                $h = imagesy($img);
                if (max($w, $h) > $max) {
                    $esc = $max / max($w, $h);
                    $img = imagescale($img, (int) round($w * $esc), (int) round($h * $esc));
                }
                imagepalettetotruecolor($img);
                imagealphablending($img, false);
                imagesavealpha($img, true);

                ob_start();
                imagewebp($img, null, 82);
                $dados = ob_get_clean();
                imagedestroy($img);

                $caminho = trim($pasta, '/').'/'.$nome.'.webp';
                Storage::disk('public')->put($caminho, $dados);

                return $caminho;
            }
        }

        // Sem GD: guarda o original.
        return $arquivo->storeAs(trim($pasta, '/'), $nome.'.'.$arquivo->extension(), 'public');
    }

    public static function apagar(?string $caminho): void
    {
        if ($caminho && ! str_starts_with($caminho, 'storage/') && ! str_contains($caminho, '..')) {
            Storage::disk('public')->delete($caminho);
        } elseif ($caminho && str_starts_with($caminho, 'storage/')) {
            Storage::disk('public')->delete(substr($caminho, strlen('storage/')));
        }
    }
}
