<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuidanceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuidanceContentController extends Controller
{
    private const CATEGORIES = ['menstrual_hygiene', 'general'];

    private function validationRules(bool $requireStatus = false): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'required|in:' . implode(',', self::CATEGORIES),
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'remove_photo' => 'sometimes|boolean',
        ];

        if ($requireStatus) {
            $rules['status'] = 'sometimes|in:published,unpublished';
        }

        return $rules;
    }

    private function formatContent(GuidanceContent $content): array
    {
        $content->loadMissing('mentor:id,name');

        return [
            'id' => $content->id,
            'mentor_id' => $content->mentor_id,
            'title' => $content->title,
            'body' => $content->body,
            'photo_url' => $content->photo_url,
            'category' => $content->category,
            'status' => $content->status,
            'language' => $content->language,
            'mentor_name' => $content->mentor?->name,
            'created_at' => $content->created_at,
            'updated_at' => $content->updated_at,
        ];
    }

    private function findOwnedContent(Request $request, int $id): GuidanceContent
    {
        return GuidanceContent::where('mentor_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();
    }

    private function handlePhoto(Request $request, ?GuidanceContent $existing = null): ?string
    {
        if ($request->boolean('remove_photo') && $existing?->photo) {
            Storage::disk('public')->delete($existing->photo);
            return null;
        }

        if ($request->hasFile('photo')) {
            if ($existing?->photo) {
                Storage::disk('public')->delete($existing->photo);
            }

            return $request->file('photo')->store('guidance_photos', 'public');
        }

        return $existing?->photo;
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // ── Mentor endpoints ──────────────────────────────────────────

    public function mentorIndex(Request $request)
    {
        $contents = GuidanceContent::where('mentor_id', $request->user()->id)
            ->with('mentor:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (GuidanceContent $c) => $this->formatContent($c));

        return response()->json([
            'success' => true,
            'data' => $contents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules(true));

        $photoPath = $this->handlePhoto($request);

        $content = GuidanceContent::create([
            'mentor_id' => $request->user()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'photo' => $photoPath,
            'category' => $validated['category'],
            'language' => 'english',
            'status' => $validated['status'] ?? 'published',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content published successfully.',
            'data' => $this->formatContent($content),
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $content = $this->findOwnedContent($request, $id);
        $validated = $request->validate($this->validationRules(true));

        $photoPath = $this->handlePhoto($request, $content);

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'photo' => $photoPath,
            'category' => $validated['category'],
            'language' => 'english',
            'status' => $validated['status'] ?? $content->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully.',
            'data' => $this->formatContent($content->fresh()),
        ]);
    }

    public function toggleUnpublish(Request $request, int $id)
    {
        $content = $this->findOwnedContent($request, $id);

        $content->status = $content->status === 'published' ? 'unpublished' : 'published';
        $content->save();

        $message = $content->status === 'published'
            ? 'Content republished successfully.'
            : 'Content unpublished successfully.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->formatContent($content->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $content = $this->findOwnedContent($request, $id);
        $this->deletePhoto($content->photo);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully.',
        ]);
    }

    // ── Public (authenticated user) endpoints ───────────────────────

    public function publicIndex(Request $request)
    {
        $request->validate([
            'category' => 'required|in:' . implode(',', self::CATEGORIES),
        ]);

        $contents = GuidanceContent::published()
            ->byCategory($request->query('category'))
            ->with('mentor:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (GuidanceContent $c) => $this->formatContent($c));

        return response()->json([
            'success' => true,
            'data' => $contents,
        ]);
    }

    public function publicShow(int $id)
    {
        $content = GuidanceContent::published()
            ->with('mentor:id,name')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatContent($content),
        ]);
    }
}
