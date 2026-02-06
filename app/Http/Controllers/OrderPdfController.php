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
            ['medicine_id' => 1,  'name' => 'મહાસુદર્શન ટીકડી', 'packing' => '200'],
            ['medicine_id' => 2,  'name' => 'સૂંઠ', 'packing' => '100 gm'],
            ['medicine_id' => 3,  'name' => 'લીંડી પિંપર', 'packing' => '100 gm'],
            ['medicine_id' => 4,  'name' => 'હરડે ટીકડી', 'packing' => '200'],
            ['medicine_id' => 5,  'name' => 'બહેડા', 'packing' => '100 gm'],
            ['medicine_id' => 6,  'name' => 'આંમળા', 'packing' => '100 gm'],
            ['medicine_id' => 7,  'name' => 'ગળો', 'packing' => '100 gm'],
            ['medicine_id' => 8,  'name' => 'ગોખરું', 'packing' => '100 gm'],
            ['medicine_id' => 9,  'name' => 'વાવડીંગ', 'packing' => '50 gm'],
            ['medicine_id' => 10, 'name' => 'નાગકેસર', 'packing' => '100 gm'],
            ['medicine_id' => 11, 'name' => 'મજીઠ', 'packing' => '100 gm'],
            ['medicine_id' => 12, 'name' => 'કડાછાલ', 'packing' => '100 gm'],
            ['medicine_id' => 13, 'name' => 'કુવાડિયા બીજ ચૂર્ણ', 'packing' => '100 gm'],
            ['medicine_id' => 14, 'name' => 'ખેરછાલ', 'packing' => '100 gm'],
            ['medicine_id' => 16, 'name' => 'હિંગ્વાષ્ટક', 'packing' => '100 gm'],
            ['medicine_id' => 15, 'name' => 'કપૂરકાચલી', 'packing' => '100 gm'],
            ['medicine_id' => 17, 'name' => 'અશ્વગંધા ટીકડી', 'packing' => '100 gm'],
            ['medicine_id' => 20, 'name' => 'ઇરિમેદાદિ તેલ', 'packing' => '25 ml'],
            ['medicine_id' => 21, 'name' => 'ષડબિંદુ તેલ', 'packing' => '25 ml'],
            ['medicine_id' => 19, 'name' => 'બિલ્વાદિ તેલ', 'packing' => '25 ml'],
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
