<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Exception;

class MenuItemController extends Controller
{
    // GET /api/index
    public function index()
    {
        try {
            return response()->json(MenuItem::all(), 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching menu items',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // POST /api/menu
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'is_available' => 'required|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $menuItem = new MenuItem();
            $menuItem->name = $validated['name'];
            $menuItem->description = $validated['description'];
            $menuItem->price = $validated['price'];
            $menuItem->is_available = $validated['is_available'];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('menu_images', 'public');
                $menuItem->image_path = $path;
            }

            $menuItem->save();

            return response()->json([
                'message' => 'Menu item created successfully',
                'menu_item' => $menuItem
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating menu item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/update/{id}
    public function show($id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            return response()->json($menuItem);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Menu item not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    // PUT /api/menu/{id}
    public function update(Request $request, $id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'is_available' => 'required|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $menuItem->name = $validated['name'];
            $menuItem->description = $validated['description'];
            $menuItem->price = $validated['price'];
            $menuItem->is_available = $validated['is_available'];

            if ($request->hasFile('image')){
                $file = $request->file('image');
                $path = $file->store('menu_images', 'public');
                $menuItem->image_path = $path;
            }

            $menuItem->save();

            return response()->json([
                'message' => 'Menu item updated successfully',
                'menu_item' => $menuItem
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating menu item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // DELETE /api/menu/{id}
    public function destroy($id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);

            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }

            $menuItem->delete();

            return response()->json([
                'message' => 'Menu item deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting menu item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
