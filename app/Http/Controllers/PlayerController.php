<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;


class PlayerController extends Controller
{
    public function index(Request $request)
    {

            $data = Player::all();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        Player::create([
            'Authorization' => $request->header('Authorization'),
            'nama' => $request->nama,
            'posisi' => $request->posisi,
            'foto' => $request->file('foto')->store('player_images', 'public'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Player created successfully'
        ]);
    }
    public function update(Request $request, $id)
    {

        $request->validate([
            'nama' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $player = Player::findOrFail($id);
        $player->nama = $request->nama;
        $player->posisi = $request->posisi;

        if ($request->hasFile('foto')) {
            $player->foto = $request->file('foto')->store('player_images', 'public');
        }

        $player->save();

        return response()->json([
            'status' => 'success',
            'message' => "Player updated successfully for ID: $id"
        ]);
    }
    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Player deleted successfully for ID: $id"
        ]);
    }

}