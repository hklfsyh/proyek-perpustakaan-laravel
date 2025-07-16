<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $loans = Loan::with(['book', 'member', 'librarian'])->latest('loan_at')->get();
            return DataTables::of($loans)
                ->addIndexColumn()
                ->addColumn('book_title', fn($loan) => $loan->book->title ?? 'N/A')
                ->addColumn('member_name', fn($loan) => $loan->member->name ?? 'N/A')
                ->addColumn('librarian_name', fn($loan) => $loan->librarian->name ?? 'N/A')
                ->addColumn('loan_at_formatted', fn($loan) => Carbon::parse($loan->loan_at)->format('d M Y, H:i'))
                ->addColumn('returned_at_formatted', function ($loan) {
                    return $loan->returned_at ? Carbon::parse($loan->returned_at)->format('d M Y, H:i') : '<span class="badge bg-warning">Belum Kembali</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (!$row->returned_at) {
                        $btn .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Return" class="btn btn-success btn-sm returnLoan">Kembalikan</a> ';
                    }
                    $btn .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteLoan">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'returned_at_formatted'])
                ->make(true);
        }

        // Ambil data untuk dropdown di form
        $books = Book::all();
        $members = User::where('role', 'member')->get();
        $librarians = User::where('role', 'librarian')->get(); // Atau bisa diganti dengan user yg login nanti

        return view('loans.index', compact('books', 'members', 'librarians'));
    }

    public function store(Request $request): JsonResponse
    {
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
        $loan->update(['returned_at' => Carbon::now()]);
        return response()->json(['success' => 'Book returned successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        Loan::find($id)->delete();
        return response()->json(['success' => 'Loan deleted successfully.']);
    }
}
