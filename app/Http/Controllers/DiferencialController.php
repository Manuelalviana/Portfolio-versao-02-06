<?php

namespace App\Http\Controllers;

use App\Models\Diferencial;
use Illuminate\Http\Request;

class DiferencialController extends Controller
{
    public function index()
    {
        // Remove a linha do Icone se não existir
        // $icones = Icone::orderBy('name')->get();
        
        // Busca apenas os diferenciais
        $diferenciais = Diferencial::orderBy('nome')->get();
        
        return view('diferenciais.index', compact('diferenciais'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'icone' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
        ]);
        
        Diferencial::create($validated);
        
        return response()->json(['success' => true]);
    }
    
    public function update(Request $request, Diferencial $diferencial)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'icone' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
        ]);
        
        $diferencial->update($validated);
        
        return response()->json(['success' => true]);
    }
    
    public function destroy(Diferencial $diferencial)
    {
        $diferencial->delete();
        
        return response()->json(['success' => true]);
    }
}