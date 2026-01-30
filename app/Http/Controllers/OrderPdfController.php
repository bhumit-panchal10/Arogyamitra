<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\{
    MedicineRequest,
    User,
    Vibhag,
    Jilla,
    Taluka,
    Gramjuth,
    Gram,
    MedicineDispatch,
    MedicineStock,
    MedicineTrack,
    Prant
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use App\Services\PdfService;
use Svg\Tag\Group;

class OrderPdfController extends Controller
{
    public function generateOrderPatrak($id = null)
    {
        $medicines = MedicineRequest::select(
            'medicine_id',
            DB::raw('SUM(delivered_quantity) as qty')
        )
            ->where('arogyamitra_id', $id)
            ->where('status', '2')
            ->groupBy('medicine_id')
            ->get();
        $medicineQtyMap = $medicines->pluck('qty', 'medicine_id')->toArray();

        $stokiestUser = User::where('id', $id)->first();
        $items = [
            ['name' => 'મહાસુદર્શન ટીકડી', 'packing' => '200'],
            ['name' => 'સૂંઠ', 'packing' => '100 gm'],
            ['name' => 'લીંડી પિંપર', 'packing' => '100 gm'],
            ['name' => 'હરડે ટીકડી', 'packing' => '200'],
            ['name' => 'બહેડા', 'packing' => '100 gm'],
            ['name' => 'આંમળા', 'packing' => '100 gm'],
            ['name' => 'ગળો', 'packing' => '100 gm'],
            ['name' => 'ગોખરું', 'packing' => '100 gm'],
            ['name' => 'વાવડીંગ', 'packing' => '50 gm'],
            ['name' => 'નાગકેસર', 'packing' => '100 gm'],
            ['name' => 'મજીઠ', 'packing' => '100 gm'],
            ['name' => 'કડાછાલ', 'packing' => '100 gm'],
            ['name' => 'કુવાડિયા બીજ', 'packing' => '100 gm'],
            ['name' => 'ખેરછાલ ચૂર્ણ', 'packing' => '100 gm'],
            ['name' => 'હિંગ્વાષ્ટક', 'packing' => '100 gm'],
            ['name' => 'કપૂરકાચલી', 'packing' => '100 gm'],
            ['name' => 'અશ્વગંધા ટીકડી', 'packing' => '100 gm'],
            ['name' => 'ઇરિમેદાદિ તેલ', 'packing' => '25 ml'],
            ['name' => 'ષડબિંદુ તેલ', 'packing' => '25 ml'],
            ['name' => 'બિલ્વાદિ તેલ', 'packing' => '25 ml'],
        ];


        // $pdf = Pdf::loadView('pdf.order_patrak', compact('items'))
        //     ->setPaper('A4', 'portrait');

        $pdf = view('pdf.order_patrak', [
            'items' => $items,
            'medicineQtyMap' => $medicineQtyMap,
            'stokiestUser' => $stokiestUser,
        ])->render();

        $mpdf = PdfService::make();

        // Write and save PDF
        $mpdf->WriteHTML($pdf);
        // Download PDF
        return response(
            $mpdf->Output('Order_Patrak.pdf', 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Order_Patrak.pdf"',
            ]
        );
    }
}
