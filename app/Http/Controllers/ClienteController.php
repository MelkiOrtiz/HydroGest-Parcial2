<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\ClienteRequest;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('apellido1_cliente')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function exportar()
    {
        $clientes = Cliente::orderBy('apellido1_cliente')->get();

        $csv = function () use ($clientes) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'DPI',
                'Nombre completo',
                'Telefono',
                'Direccion',
                'Numero de cuenta',
                'Estado',
            ]);

            foreach ($clientes as $cliente) {
                fputcsv($handle, [
                    $cliente->dpi_cliente,
                    $cliente->nombre_completo,
                    $cliente->telefono_cliente,
                    $cliente->direccion_cliente,
                    $cliente->numero_cuenta_cliente,
                    $cliente->activo_cliente === 'ACTIVO' ? 'Activo' : 'No activo',
                ]);
            }

            fclose($handle);
        };

        $nombreArchivo = 'clientes_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload($csv, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(ClienteRequest $request)
    {
        Cliente::create($request->validated());
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('contadores');
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->contadores()->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar: el cliente tiene contadores asignados.');
        }

        $cliente->delete();
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}