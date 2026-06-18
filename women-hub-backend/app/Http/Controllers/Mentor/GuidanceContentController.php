<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\GuidanceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuidanceContentController extends Controller
{
    private function mentor()
    {
        return Auth::guard('mentor')->user();
    }

    public function hub()
    {
        return view('mentor.guidance.index');
    }

    public function general()
    {
        $mentor = $this->mentor();
        $general = GuidanceContent::where('mentor_id', $mentor->id)
            ->where('category', 'general')
            ->orderByDesc('created_at')
            ->get();

        return view('mentor.guidance.general.index', compact('general'));
    }

    public function createGeneral()
    {
        return view('mentor.guidance.general.create');
    }

    public function storeGeneral(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('guidance_photos', 'public');
        }

        GuidanceContent::create([
            'mentor_id' => $this->mentor()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'photo' => $photoPath,
            'category' => 'general',
            'language' => 'english',
            'status' => $request->has('is_published') ? 'published' : 'unpublished',
        ]);

        return redirect()->route('mentor.general')
            ->with('success', 'General content created successfully!');
    }

    public function editGeneral($id)
    {
        $content = $this->findOwned($id, 'general');
        return view('mentor.guidance.general.edit', compact('content'));
    }

    public function updateGeneral(Request $request, $id)
    {
        $content = $this->findOwned($id, 'general');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'remove_photo' => 'sometimes|boolean',
        ]);

        $photoPath = $content->photo;

        if ($request->boolean('remove_photo') && $photoPath) {
            Storage::disk('public')->delete($photoPath);
            $photoPath = null;
        }

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('guidance_photos', 'public');
        }

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'photo' => $photoPath,
            'language' => 'english',
            'status' => $request->has('is_published') ? 'published' : 'unpublished',
        ]);

        return redirect()->route('mentor.general')
            ->with('success', 'General content updated successfully!');
    }

    public function hygiene()
    {
        $mentor = $this->mentor();
        $hygiene = GuidanceContent::where('mentor_id', $mentor->id)
            ->where('category', 'menstrual_hygiene')
            ->orderByDesc('created_at')
            ->get();

        return view('mentor.guidance.hygiene.index', compact('hygiene'));
    }

    public function createHygiene()
    {
        return view('mentor.guidance.hygiene.create');
    }

    public function storeHygiene(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('guidance_photos', 'public');
        }

        GuidanceContent::create([
            'mentor_id' => $this->mentor()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'photo' => $photoPath,
            'category' => 'menstrual_hygiene',
            'language' => 'english',
            'status' => $request->has('is_published') ? 'published' : 'unpublished',
        ]);

        return redirect()->route('mentor.hygiene')
            ->with('success', 'Menstrual hygiene content created successfully!');
    }

    public function editHygiene($id)
    {
        $content = $this->findOwned($id, 'menstrual_hygiene');
        return view('mentor.guidance.hygiene.edit', compact('content'));
    }

    public function updateHygiene(Request $request, $id)
    {
        $content = $this->findOwned($id, 'menstrual_hygiene');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'remove_photo' => 'sometimes|boolean',
        ]);

        $photoPath = $content->photo;

        if ($request->boolean('remove_photo') && $photoPath) {
            Storage::disk('public')->delete($photoPath);
            $photoPath = null;
        }

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('guidance_photos', 'public');
        }

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'photo' => $photoPath,
            'language' => 'english',
            'status' => $request->has('is_published') ? 'published' : 'unpublished',
        ]);

        return redirect()->route('mentor.hygiene')
            ->with('success', 'Menstrual hygiene content updated successfully!');
    }

    public function destroy($id)
    {
        $content = GuidanceContent::where('mentor_id', $this->mentor()->id)
            ->where('id', $id)
            ->firstOrFail();

        $redirect = $content->category === 'menstrual_hygiene'
            ? route('mentor.hygiene')
            : route('mentor.general');

        if ($content->photo) {
            Storage::disk('public')->delete($content->photo);
        }

        $content->delete();

        return redirect($redirect)->with('success', 'Content deleted successfully!');
    }

    public function publish($id)
    {
        $content = GuidanceContent::where('mentor_id', $this->mentor()->id)
            ->where('id', $id)
            ->firstOrFail();

        $content->update(['status' => 'published']);

        return redirect()->back()->with('success', 'Content published successfully!');
    }

    public function unpublish($id)
    {
        $content = GuidanceContent::where('mentor_id', $this->mentor()->id)
            ->where('id', $id)
            ->firstOrFail();

        $content->update(['status' => 'unpublished']);

        return redirect()->back()->with('success', 'Content unpublished successfully!');
    }

    private function findOwned(int $id, string $category): GuidanceContent
    {
        return GuidanceContent::where('mentor_id', $this->mentor()->id)
            ->where('id', $id)
            ->where('category', $category)
            ->firstOrFail();
    }
}
