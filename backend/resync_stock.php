\Illuminate\Support\Facades\DB::table('lona_tallas')->truncate();
$variantes = \App\Models\VarianteProducto::whereNotNull('lona_id')->where('stock', '>', 0)->get();
foreach($variantes as $v) {
    $lt = \App\Models\LonaTalla::firstOrCreate(
        ['lona_id' => $v->lona_id, 'talla' => $v->talla],
        ['cantidad' => 0]
    );
    $lt->cantidad += $v->stock;
    $lt->save();
}
echo "Synced " . $variantes->count() . " variantes to lona_tallas.\n";
