<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if (!Gate::allows('manage-library')) {
            abort(403);
        }

        if ($request->ajax()) {
            $loans = Loan::with(['book', 'member', 'librarian'])->latest('loan_at')->get();
            return DataTables::of($loans)
                ->addIndexColumn()
                ->addColumn('book_title', fn($loan) => $loan->book->title ?? 'N/A')
                ->addColumn('member_name', fn($loan) => $loan->member->name ?? 'N/A')
                ->addColumn('librarian_name', fn($loan) => $loan->librarian->name ?? 'N/A')
                ->addColumn('loan_at_formatted', fn($loan) => Carbon::parse($loan->loan_at)->format('d M Y, H:i'))
                ->addColumn('returned_at_formatted', function ($loan) {
                    return $loan->returned_at ? Carbon::parse($loan->returned_at)->format('d M Y, H:i') : '<span class="badge bg-warning text-dark">Belum Kembali</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (!$row->returned_at) {
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm returnLoan">Kembalikan</a> ';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteLoan">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'returned_at_formatted'])
                ->make(true);
        }

        $books = Book::whereDoesntHave('loans', function ($query) {
            $query->whereNull('returned_at');
        })->get();

        $members = User::where('role', 'member')->get();
        $librarians = User::whereIn('role', ['librarian', 'admin'])->get();

        return view('loans.index', compact('books', 'members', 'librarians'));
    }

    public function store(Request $request): JsonResponse
    {
        if (!Gate::allows('manage-library')) {
            abort(403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:users,id',
            'librarian_id' => 'required|exists:users,id',
        ]);

        Loan::create([
            'book_id' => $request->book_id,
            'member_id' => $request->member_id,
            'librarian_id' => $request->librarian_id,
            'loan_at' => Carbon::now(),
            'note' => $request->note
        ]);

        return response()->json(['success' => 'Loan saved successfully.']);
    }

    public function returnBook(Loan $loan): JsonResponse
    {
        if (!Gate::allows('manage-library')) {
            abort(403);
        }

        $loan->update(['returned_at' => Carbon::now()]);
        return response()->json(['success' => 'Book returned successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        if (!Gate::allows('manage-library')) {
            abort(403);
        }

        Loan::find($id)->delete();
        return response()->json(['success' => 'Loan deleted successfully.']);
    }

    // ================== FUNGSI BARU UNTUK MEMBER ==================
    /**
     * Menangani aksi peminjaman buku oleh member.
     */
    public function borrow(Book $book): JsonResponse
    {
        // Validasi: Cek apakah buku ini sedang dipinjam dan belum dikembalikan.
        $existingLoan = Loan::where('book_id', $book->id)->whereNull('returned_at')->exists();
        if ($existingLoan) {
            return response()->json(['error' => 'Gagal, buku ini sedang dipinjam oleh orang lain.'], 422);
        }

        // Ambil librarian pertama sebagai petugas default yang tercatat
        $librarian = User::where('role', 'librarian')->first() ?? User::where('role', 'admin')->first();

        // Jika tidak ada admin atau librarian sama sekali di sistem
        if (!$librarian) {
            return response()->json(['error' => 'Tidak ada petugas yang tersedia di sistem untuk mencatat peminjaman.'], 500);
        }

        // Buat data peminjaman baru
        Loan::create([
            'book_id' => $book->id,
            'member_id' => Auth::id(), // ID diambil dari member yang sedang login
            'librarian_id' => $librarian->id, // ID diambil dari petugas default
            'loan_at' => Carbon::now(),
        ]);

        return response()->json(['success' => 'Buku berhasil dipinjam!']);
    }
}
