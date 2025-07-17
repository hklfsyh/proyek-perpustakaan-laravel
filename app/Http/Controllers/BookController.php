<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BookController extends Controller
{
    // Fungsi ini sekarang dipakai oleh SEMUA role untuk menampilkan view dan data AJAX
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $books = Book::with(['categories', 'loans' => fn($q) => $q->whereNull('returned_at')])->latest()->get();
            return DataTables::of($books)
                ->addIndexColumn()
                ->addColumn('categories', fn($b) => $b->categories->pluck('name')->implode(', '))
                ->addColumn('action', function ($row) {
                    if (Auth::check()) {
                        $user = Auth::user();
                        if ($user->role == 'admin' || $user->role == 'librarian') {
                            $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm editBook">Edit</a>';
                            $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteBook">Delete</a>';
                            return $btn;
                        } elseif ($user->role == 'member') {
                            if ($row->loans->isNotEmpty()) {
                                return '<span class="badge bg-secondary">Dipinjam</span>';
                            }
                            return '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm borrowBook">Pinjam</a>';
                        }
                    }
                    // Jika guest, tampilkan tombol login
                    return '<a href="' . route('login') . '" class="btn btn-outline-primary btn-sm">Login untuk Pinjam</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = Category::all();
        return view('books.index', compact('categories'));
    }

    // Fungsi BARU: Hanya untuk menyediakan data publik tanpa tombol aksi
    public function catalogData(Request $request): JsonResponse
    {
        $books = Book::with('categories')->latest()->get();
        return DataTables::of($books)
            ->addIndexColumn()
            ->addColumn('categories', fn($b) => $b->categories->pluck('name')->implode(', '))
            ->make(true);
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
