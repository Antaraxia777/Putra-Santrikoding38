<?php

namespace App\Http\Controllers;

//import model product
use App\Models\Product; 

//import return type View
use Illuminate\View\View;

//import return type redirectResponse
use Illuminate\Http\Request;

//import Http Request
use Illuminate\Http\RedirectResponse;

//import Facades Storage
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index() : View
    {
        //get all products
        $products = Product::latest()->paginate(10);

        //render view with products
        return view('products.index', compact('products'));
    }

    /**
     * create
     *
     * @return View
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        //validate form
        $validated = $request->validate([
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        try {
            //upload image
            $image = $request->file('image');
            
            // Generate unique filename
            $imageName = $image->hashName();
            
            // Store image to storage/app/public/products
            $path = $image->storeAs('public/products', $imageName);
            
            // Create product
            Product::create([
                'image'         => $imageName,
                'title'         => $validated['title'],
                'description'   => $validated['description'],
                'price'         => $validated['price'],
                'stock'         => $validated['stock']
            ]);

            //redirect to index
            return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
            
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * show
     *
     * @param  mixed $id
     * @return View
     */
    public function show(string $id): View
    {
        //get product by ID
        $product = Product::findOrFail($id);

        //render view with product
        return view('products.show', compact('product'));
    }
    
    /**
     * edit
     *
     * @param  mixed $id
     * @return View
     */
    public function edit(string $id): View
    {
        //get product by ID
        $product = Product::findOrFail($id);

        //render view with product
        return view('products.edit', compact('product'));
    }
        
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $validated = $request->validate([
            'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        //get product by ID
        $product = Product::findOrFail($id);

        try {
            //check if image is uploaded
            if ($request->hasFile('image')) {
                //delete old image
                Storage::delete('public/products/'.$product->image);

                //upload new image
                $image = $request->file('image');
                $imageName = $image->hashName();
                $image->storeAs('public/products', $imageName);

                //update product with new image
                $product->update([
                    'image'         => $imageName,
                    'title'         => $validated['title'],
                    'description'   => $validated['description'],
                    'price'         => $validated['price'],
                    'stock'         => $validated['stock']
                ]);
            } else {
                //update product without image
                $product->update([
                    'title'         => $validated['title'],
                    'description'   => $validated['description'],
                    'price'         => $validated['price'],
                    'stock'         => $validated['stock']
                ]);
            }

            //redirect to index
            return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
            
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Gagal mengupdate data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * destroy
     *
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        //get product by ID
        $product = Product::findOrFail($id);

        try {
            //delete image
            Storage::delete('public/products/'. $product->image);

            //delete product
            $product->delete();

            //redirect to index
            return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
            
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
