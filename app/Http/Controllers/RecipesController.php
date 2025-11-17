<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Recipes;

class RecipesController extends Controller
{
    public function getRecipes(){
        $recipes = Recipes::all();
        $recipes = $recipes->map(function($recipe) {
            $recipe->ingredients = json_decode($recipe->ingredients);
            $recipe->instructions = json_decode($recipe->instructions);
            
            //Agregar URL completa para la imagen
            if ($recipe->image) {
                $recipe->image = url($recipe->image);
                // Esto convierte: images/1762741819_... 
                // En: http://localhost:8000/images/1762741819_...
            }
            
            return $recipe;
        });
        return response()->json(["message"=>'Get recipes','data'=>$recipes],200);
    }

    public function postRecipe(Request $request){
        // Validar el request data
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|min:3',
            'ingredients'=>'required|string', // viene como JSON string
            'instructions'=>'required|string', // viene como JSON string
            'prepTimeMinutes'=>'required|integer|min:1',
            'cookTimeMinutes'=>'required|integer|min:1',
            'servings'=>'required|integer|min:1',
            'difficulty'=>'required|string',
            'cuisine'=>'required|string',
            'caloriesPerServing'=>'required|integer|min:1',
            'image'=>'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if($validator->fails()){
            return response()->json([
                'message'=>'validation error',
                'errors'=>$validator->errors()
            ], 422);
        }

        // Procesar la imagen DESPUÉS de validar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $nombreArchivo = time() . '_' . $image->getClientOriginalName();
            $rutaDestino = public_path('images');
            
            // Crear directorio si no existe
            if (!file_exists($rutaDestino)) {
                mkdir($rutaDestino, 0755, true);
            }
            
            $image->move($rutaDestino, $nombreArchivo);
            $imagePath = 'images/' . $nombreArchivo;
        }

        // Decodificar JSON de ingredients e instructions
        $ingredients = json_decode($request->ingredients, true);
        $instructions = json_decode($request->instructions, true);

        // Crear la receta
        $recipe = new Recipes();
        $recipe->name = $request->name;
        $recipe->ingredients = json_encode($ingredients); // Re-codificar limpio
        $recipe->instructions = json_encode($instructions); // Re-codificar limpio
        $recipe->prepTimeMinutes = $request->prepTimeMinutes;
        $recipe->cookTimeMinutes = $request->cookTimeMinutes;
        $recipe->servings = $request->servings;
        $recipe->difficulty = $request->difficulty;
        $recipe->cuisine = $request->cuisine;
        $recipe->caloriesPerServing = $request->caloriesPerServing;
        $recipe->image = $imagePath; //Usar la ruta guardada
        $recipe->save();

        return response()->json([
            "message"=>"New recipe created",
            "data"=>$recipe
        ], 201);
    }

    public function updateRecipe(Request $request, string $id){
        
        $recipe = Recipes::find($id);

        if(!$recipe){
            return response()->json(['message'=>'Recipe not found'], 404);
        }

        // Validación dinámica
        $rules = [
            'name'=>'required|string|min:3',
            'ingredients'=>'required|string',
            'instructions'=>'required|string',
            'prepTimeMinutes'=>'required|integer|min:1',
            'cookTimeMinutes'=>'required|integer|min:1',
            'servings'=>'required|integer|min:1',
            'difficulty'=>'required|string',
            'cuisine'=>'required|string',
            'caloriesPerServing'=>'required|integer|min:1',
        ];

        // Solo validar imagen si viene un archivo
        if ($request->hasFile('image')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'message'=>'Validation error',
                'errors'=>$validator->errors()
            ], 422);
        }

        // Procesar imagen solo si viene archivo nuevo
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior
            if ($recipe->image && $recipe->image !== '') {
                $oldImagePath = public_path($recipe->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Guardar nueva imagen
            $image = $request->file('image');
            $nombreArchivo = time() . '_' . $image->getClientOriginalName();
            $rutaDestino = public_path('images');
            
            if (!file_exists($rutaDestino)) {
                mkdir($rutaDestino, 0755, true);
            }
            
            $image->move($rutaDestino, $nombreArchivo);
            $recipe->image = 'images/' . $nombreArchivo;
        }
        // Si no viene archivo, mantener imagen actual (no hacer nada)

        // Decodificar JSON strings
        $ingredients = json_decode($request->ingredients, true);
        $instructions = json_decode($request->instructions, true);

        // Actualizar campos
        $recipe->name = $request->name;
        $recipe->ingredients = json_encode($ingredients);
        $recipe->instructions = json_encode($instructions);
        $recipe->prepTimeMinutes = $request->prepTimeMinutes;
        $recipe->cookTimeMinutes = $request->cookTimeMinutes;
        $recipe->servings = $request->servings;
        $recipe->difficulty = $request->difficulty;
        $recipe->cuisine = $request->cuisine;
        $recipe->caloriesPerServing = $request->caloriesPerServing;
        
        $recipe->save();

        return response()->json([
            'message'=>'Recipe updated successfully',
            'data'=>$recipe
        ], 200);
    }

    public function deleteRecipe(string $id){
        
        $recipe = Recipes::find($id);

        if(!$recipe){
            return response()->json(['message'=>'Recipe not found'], 404);
        }

        //Eliminar la imagen del servidor antes de eliminar el registro
        $oldImagePath = public_path($recipe->image);
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        // Eliminar el registro de la base de datos
        $recipe->delete();

        return response()->json([
            'message'=>'Recipe deleted successfully',
            'id'=>$id
        ], 200);
    }
}
