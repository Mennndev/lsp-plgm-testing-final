<?php

namespace App\Http\Controllers;

use App\Models\ProgramPelatihan;
use Illuminate\Http\Request;

class Skemacontroller extends Controller
{
    public function index()
    {
        // Ambil semua program yang dipublish.
        $programs = ProgramPelatihan::where('is_published', 1)->get();

        return view('skema.index', compact('programs'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(ProgramPelatihan $program)
    {
        $program->load([
            'units' => function ($q) {
                $q->orderBy('no_urut');
            },
            'profesiTerkait',
        ]);

        // Field Metode Asesmen dapat berasal dari editor HTML. Nilai editor
        // yang tampak kosong sering tersimpan sebagai <p><br></p>. View publik
        // memakai teks per baris, jadi normalisasikan HTML menjadi plain text
        // agar tag HTML tidak tampil sebagai tulisan di halaman skema.
        $program->metode_asesmen = $this->normalizeMetodeAsesmen($program->metode_asesmen);

        return view('skema.show', [
            'program' => $program,
        ]);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    private function normalizeMetodeAsesmen(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        // Pertahankan pemisah baris dari elemen block/line break sebelum tag dihapus.
        $text = preg_replace('/<br\s*\/?>/i', "\n", $value) ?? $value;
        $text = preg_replace('/<\/(p|div|li|h[1-6])>/i', "\n", $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $lines = array_values(array_filter(
            array_map(fn ($line) => trim($line), $lines),
            fn ($line) => $line !== ''
        ));

        return implode("\n", $lines);
    }
}
