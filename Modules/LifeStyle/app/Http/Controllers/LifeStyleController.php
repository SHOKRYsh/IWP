<?php

namespace Modules\LifeStyle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\LifeStyle\Models\LifeStyle;

class LifeStyleController extends Controller
{
    public function index()
    {
        $lifeStyles = LifeStyle::paginate();
        return $this->respondOk($lifeStyles, 'LifeStyles retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_key' => 'required|string|max:255',
        ]);

        $lifeStyle = LifeStyle::create($validated);

        return $this->respondCreated($lifeStyle, 'LifeStyle created successfully');
    }

    public function show($id)
    {
        $lifeStyle = LifeStyle::with('elements.taskTypes')->findOrFail($id);
        return $this->respondOk($lifeStyle, 'LifeStyle retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $lifeStyle = LifeStyle::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon_key' => 'sometimes|required|string|max:255',
        ]);

        $lifeStyle->update($validated);

        return $this->respondOk($lifeStyle, 'LifeStyle updated successfully');
    }

    public function destroy($id)
    {
        $lifeStyle = LifeStyle::findOrFail($id);

        if ($lifeStyle->elements()->count() > 0) {
            return $this->respondNotFound(null, 'LifeStyle has elements');
        }

        $lifeStyle->delete();

        return $this->respondOk(null, 'LifeStyle deleted successfully');
    }
}
