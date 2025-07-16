<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BookController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen buku.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $books = Book::with('categories')->latest()->get();
            return DataTables::of($books)
                ->addIndexColumn()
                ->addColumn('categories', function ($book) {
                    return $book->categories->pluck('name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editBook">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteBook">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = Category::all();
        return view('books.index', compact('categories'));
    }

    /**
     * Menyimpan data buku baru atau perubahan data buku.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'required|string|max:255',
            'categories' => 'required|array'
        ]);

        $book = Book::updateOrCreate(
            ['id' => $request->book_id],
            [
                'title' => $request->title,
                'description' => $request->description,
                'authors' => $request->authors,
                'isbn' => $request->isbn
            ]
        );

        $book->categories()->sync($request->categories);

        return response()->json(['success' => 'Book saved successfully.']);
    }

    /**
     * Mengambil data buku untuk diedit.
     */
    public function edit($id): JsonResponse
    {
        $book = Book::with('categories')->find($id);
        return response()->json($book);
    }

    /**
     * Menghapus data buku.
     */
    public function destroy($id): JsonResponse
    {
        Book::find($id)->delete();
        return response()->json(['success' => 'Book deleted successfully.']);
    }
}
