<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\MarketplacePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $marketplaces = Marketplace::query()
            ->with('user')
            ->when($search, fn ($q) => $q->where('product_name', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(9);

        return view('marketplaces.index', compact('marketplaces', 'search'));
    }

    public function create()
    {
        return view('marketplaces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'seller_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'image.max' => 'Ukuran foto maksimal 10 MB.',
            'image.image' => 'File yang diunggah harus berupa foto.',
            'image.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
        ]);

        Marketplace::create([
            'user_id' => Auth::id(),
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'product_status' => 'tersedia',
            'seller_phone' => $request->seller_phone,
            'image' => $this->storeImage($request),
        ]);

        return redirect()->route('marketplaces.index')
            ->with('success', 'Produk berhasil didaftarkan.');
    }

    public function show(Marketplace $marketplace)
    {
        return view('marketplaces.show', compact('marketplace'));
    }

    public function edit(Marketplace $marketplace)
    {
        $this->authorizeSellerOrAdmin($marketplace);

        return view('marketplaces.edit', compact('marketplace'));
    }

    public function update(Request $request, Marketplace $marketplace)
    {
        $this->authorizeSellerOrAdmin($marketplace);

        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'product_status' => 'nullable|in:tersedia,habis',
            'seller_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'image.max' => 'Ukuran foto maksimal 10 MB.',
            'image.image' => 'File yang diunggah harus berupa foto.',
            'image.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
        ]);

        $data = [
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'product_status' => $request->product_status ?? 'tersedia',
            'seller_phone' => $request->seller_phone,
        ];

        if ($request->hasFile('image')) {
            if ($marketplace->image) {
                Storage::disk('public')->delete($marketplace->image);
            }

            $data['image'] = $this->storeImage($request);
        }

        $marketplace->update($data);

        return redirect()->route('marketplaces.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Marketplace $marketplace)
    {
        $this->authorizeSellerOrAdmin($marketplace);

        if ($marketplace->image) {
            Storage::disk('public')->delete($marketplace->image);
        }

        $marketplace->delete();

        return redirect()->route('marketplaces.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function buy(Marketplace $marketplace)
    {
        if ($marketplace->product_status !== 'tersedia' || $marketplace->stock < 1) {
            return back()->with('error', 'Produk sedang tidak tersedia.');
        }

        $marketplace->decrement('stock');
        if ($marketplace->stock === 0) {
            $marketplace->update(['product_status' => 'habis']);
        }

        MarketplacePurchase::create([
            'marketplace_id' => $marketplace->id,
            'buyer_id' => Auth::id(),
            'seller_id' => $marketplace->user_id,
            'product_name' => $marketplace->product_name,
            'price' => $marketplace->price,
            'quantity' => 1,
            'total_price' => $marketplace->price,
            'status' => 'menunggu',
        ]);

        return back()->with('success', "Pembelian {$marketplace->product_name} berhasil diproses.");
    }

    public function myPurchases()
    {
        $purchases = MarketplacePurchase::query()
            ->with(['marketplace.user', 'seller'])
            ->where('buyer_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(10);

        return view('marketplaces.purchases', compact('purchases'));
    }

    public function mySales()
    {
        $sales = MarketplacePurchase::query()
            ->with(['marketplace', 'buyer'])
            ->where('seller_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(10);

        return view('marketplaces.sales', compact('sales'));
    }

    public function updatePurchaseStatus(Request $request, MarketplacePurchase $purchase)
    {
        abort_unless(
            Auth::user()->role === 'admin' || Auth::id() === $purchase->seller_id,
            403
        );

        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai',
        ]);

        $purchase->update(['status' => $request->status]);

        return back()->with('success', 'Status pembelian berhasil diperbarui.');
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');

        if (! $file->isValid() || ! is_file($file->getPathname())) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $name = Str::random(40).'.'.$extension;

        Storage::disk('public')->put(
            "marketplace/{$name}",
            file_get_contents($file->getPathname())
        );

        return "marketplace/{$name}";
    }

    private function authorizeSellerOrAdmin(Marketplace $marketplace): void
    {
        abort_unless(
            Auth::user()->role === 'admin' || Auth::id() === $marketplace->user_id,
            403
        );
    }
}
