<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\History;
use App\Models\JenisHak;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Proses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function infoBerkas(){

        return view('laporan.info_berkas',[
            'title' => 'Informasi Berkas',
            'kecamatan' => Kecamatan::all(),
            'kelurahan' => Kelurahan::all(),
            'jenis_hak' => JenisHak::all(),
        ]);
    }

    public function dtInfoBerkas(Request $request){

        $berkas = Berkas::where('no_hak',sprintf("%05d", $request->no_hak))->where('kecamatan_id',$request->kecamatan_id)->where('kelurahan_id',$request->kelurahan_id)->where('jenis_hak_id',$request->jenis_hak_id)->with(['kecamatan','kelurahan'])->latest('id')->first();

        if ($berkas) {
            $dt_history = History::where('berkas_id',$berkas->id)->with('user')->get();
        } else {
            $dt_history = null;
        }

        $proses = Proses::where('id','!=',5)->get();

        $dt_proses = [];
        foreach ($proses as $p) {
            $dt = $dt_history->where('proses_id',$p->id)->first();
            $dt_proses [] = [
                'nm_proses' => $p->nm_proses,
                'ada' => $dt ? 1 : 0,
                'selesai' => $dt ? ($dt->selesai != null ? 1 : 0) :0,
                'petugas' => $dt ? ($dt->petugas ? $dt->petugas->name : '') :'',
            ];
        }
        
        

        return view('laporan.dt_info_berkas',[
            'title' => 'Informasi Berkas',
            'berkas' => $berkas,
            'dt_proses' => $dt_proses,
        ])->render();
    }

    public function berkasTunggakan(){
        return view('laporan.tunggakan_berkas',[
            'title' => 'Tunggakan Berkas',
        ]);
    }

    public function getListTunggakan(){
        $pengajuan = Berkas::select('berkas.*')->selectRaw("dt_berkas.tanggal, dt_berkas.dt_hak, dt_berkas.dt_peta, dt_berkas.lama_tgl")
        ->leftJoin(
            DB::raw("(SELECT berkas.id, datediff(current_date(), berkas.created_at) as lama_tgl, CONCAT(DATE_FORMAT(berkas.created_at, '%d-%m-%Y'), '<br>', datediff(current_date(), berkas.created_at), ' Hari') as tanggal, CONCAT(kode_hak,'-',no_hak) as dt_hak, CONCAT(nm_jenis_peta,'-',no_peta,'-',tahun_peta) as dt_peta FROM berkas
            LEFT JOIN jenis_hak ON berkas.jenis_hak_id = jenis_hak.id
            LEFT JOIN jenis_peta ON berkas.jenis_peta_id = jenis_peta.id
            GROUP BY berkas.id) dt_berkas"), 
                'berkas.id', '=', 'dt_berkas.id'
        )
        ->where('selesai',0)->whereRaw(" dt_berkas.lama_tgl > 7")->orderBy('berkas.id','ASC')->with(['kecamatan','kelurahan']);

        return datatables()->of($pengajuan)
                        ->addColumn('action', function($data){
                            
                            $button = '';
                            
                            $button .= '<button data-bs-toggle="modal" data-bs-target="#modal_info_tunggakan" class="btn btn-xs btn-info btn_info_tunggakan"  berkas_id="'.$data->id .'" ><i class="bx bx-search-alt"></i></button>';

                            return $button;
                            
                        })

                        // ->setRowClass(function ($data) {
                        //     return $data->lama_tgl > 7 ? 'blink' : '';
                        // })
                        
                        ->rawColumns(['tanggal','action'])                        
                        ->addIndexColumn()
                        ->make(true);
    }

    public function dtInfoTunggakan($berkas_id){

        $berkas = Berkas::where('id',$berkas_id)->with(['kecamatan','kelurahan'])->first();

        if ($berkas) {
            $dt_history = History::where('berkas_id',$berkas->id)->with('user')->get();
        } else {
            $dt_history = null;
        }

        $proses = Proses::all();

        $dt_proses = [];
        foreach ($proses as $p) {
            $dt = $dt_history->where('proses_id',$p->id)->first();
            $dt_proses [] = [
                'nm_proses' => $p->nm_proses,
                'ada' => $dt ? 1 : 0,
                'selesai' => $dt ? ($dt->selesai != null ? 1 : 0) :0,
                'petugas' => $dt ? ($dt->petugas ? $dt->petugas->name : '') :'',
            ];
        }
        
        

        return view('laporan.dt_info_berkas',[
            'title' => 'Informasi Berkas',
            'berkas' => $berkas,
            'dt_proses' => $dt_proses,
        ])->render();
    }


    public function berkasSelesai(){
        return view('laporan.berkas_selesai',[
            'title' => 'Berkas Selesai',
        ]);
    }

    public function getListSelesai(){
        $pengajuan = Berkas::select('berkas.*')->selectRaw("dt_berkas.tanggal, dt_berkas.tanggal_selesai, dt_berkas.lama_pengerjaan, dt_berkas.dt_hak, dt_berkas.dt_peta, dt_berkas.lama_tgl")
        ->leftJoin(
            DB::raw("(SELECT berkas.id, datediff(current_date(), berkas.created_at) as lama_tgl, DATE_FORMAT(berkas.created_at, '%d-%m-%Y') as tanggal, DATE_FORMAT(berkas.updated_at, '%d-%m-%Y') tanggal_selesai, CONCAT(datediff(berkas.updated_at, berkas.created_at),' Hari') as lama_pengerjaan, CONCAT(kode_hak,'-',no_hak) as dt_hak, CONCAT(nm_jenis_peta,'-',no_peta,'-',tahun_peta) as dt_peta FROM berkas
            LEFT JOIN jenis_hak ON berkas.jenis_hak_id = jenis_hak.id
            LEFT JOIN jenis_peta ON berkas.jenis_peta_id = jenis_peta.id
            GROUP BY berkas.id) dt_berkas"), 
                'berkas.id', '=', 'dt_berkas.id'
        )
        ->where('selesai',1)->orderBy('berkas.id','DESC')->with(['kecamatan','kelurahan']);

        return datatables()->of($pengajuan)
                        // ->addColumn('action', function($data){
                            
                        //     $button = '';
                            
                        //     $button .= '<button data-bs-toggle="modal" data-bs-target="#modal_info_tunggakan" class="btn btn-xs btn-info btn_info_tunggakan"  berkas_id="'.$data->id .'" ><i class="bx bx-search-alt"></i></button>';

                        //     return $button;
                            
                        // })

                        // ->setRowClass(function ($data) {
                        //     return $data->lama_tgl > 7 ? 'blink' : '';
                        // })
                        
                        // ->rawColumns(['tanggal','action'])                        
                        ->addIndexColumn()
                        ->make(true);
    }

    public function laporanPerhari(){
        // $proses = Proses::whereIn('id',['3,4,5,6,7'])->get();
        $tgl1 = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d"))));
            $tgl2 = date('Y-m-d');
        $user = User::where('jenis_user_id',3)->get();
        $periode = History::select(DB::raw('DATE(created_at) as date'))->where('created_at','>=',$tgl1.' 00:00:01')->where('created_at','<=',$tgl2.' 23:59:59')->groupBy('date')->get();
        $history = History::select(DB::raw('DATE(selesai) as date'))->selectRaw("COUNT(id) as jml, user_id")->where('selesai','>=',$tgl1.' 00:00:01')->where('selesai','<=',$tgl2.' 23:59:59')->where('selesai','!=',NULL)->groupBy('user_id')->groupBy('date')->get();

        $dt_laporan = [];
        foreach ($user as $u) {
            $dt_history = [];
            foreach ($periode as $d) {
                $dhistory = $history->where('user_id',$u->id)->where('date',$d->date)->first();
                $dt_history [] = [
                    'tgl' => $d->date,
                    'jml' => $dhistory ? $dhistory->jml : 0,
                ];
            }

            $dt_laporan [] = [
                'nm_user' => $u->name,
                'user_id' => $u->id,
                'dt_history' => $dt_history,
            ];
        }

        return view('laporan.perhari',[
            'title' => 'Laporan Perhari',
            'periode' => $periode,
            'dt_laporan' => $dt_laporan
        ]);
    }

    public function getPekerjaanPerhari($user_id,$tgl){
        $tgl1 = $tgl.' 00:00:01';
        $tgl2 = $tgl.' 23:59:59';
        return view('laporan.dt_info_perkerjaan',[
            'dt_berkas' => History::where('selesai','>=',$tgl1)->where('selesai','<=',$tgl2)->where('user_id',$user_id)->get(),
        ])->render();
    }

    public function laporanPerproses(){
        return view('laporan.perproses',[
            'title' => 'Laporan Perproses',
            'dt_berkas' => History::select('history.*')->selectRaw("COUNT(id) as jml")->where('selesai',NULL)->groupBy('proses_id')->get(),
            'perpelayanan' => Berkas::select('berkas.*')->selectRaw("COUNT(id) as jml")->where('selesai',0)->groupBy('pelayanan_id')->get(),
        ]);
    }

    public function getLaporanPerproses($proses_id){
        return view('laporan.dt_info_perproses',[
            'dt_berkas' => History::where('selesai',NULL)->where('proses_id',$proses_id)->get(),
        ])->render();
    }

    public function getLaporanPerpelayanan($pelayanan_id){
        return view('laporan.dt_info_perpelayanan',[
            'dt_berkas' => Berkas::where('selesai',0)->where('pelayanan_id',$pelayanan_id)->get(),
        ])->render();
    }

    
    public function tutupBerkas($berkas_id){
        Berkas::where('id',$berkas_id)->update(['selesai'=>2]);
        History::where('berkas_id',$berkas_id)->where('selesai',NULL)->update([
            'selesai' => date('Y-m-d H:i:s'),
            'user_id' => Auth::id(),
        ]);
    }

    public function dtInfoBerkasGet($berkas_id){

        $berkas = Berkas::where('id',$berkas_id)->first();

        if ($berkas) {
            $dt_history = History::where('berkas_id',$berkas->id)->with('user')->get();
        } else {
            $dt_history = null;
        }

        $proses = Proses::where('id','!=',5)->get();

        $dt_proses = [];
        foreach ($proses as $p) {
            $dt = $dt_history->where('proses_id',$p->id)->first();
            $dt_proses [] = [
                'nm_proses' => $p->nm_proses,
                'ada' => $dt ? 1 : 0,
                'selesai' => $dt ? ($dt->selesai != null ? 1 : 0) :0,
                'petugas' => $dt ? ($dt->petugas ? $dt->petugas->name : '') :'',
            ];
        }
        
        

        return view('laporan.dt_info_berkas',[
            'title' => 'Informasi Berkas',
            'berkas' => $berkas,
            'dt_proses' => $dt_proses,
        ])->render();
    }

    public function dashboard(Request $request){

        if ($request->query('tgl1')) {
            $tgl1 = $request->query('tgl1');
            $tgl2 = $request->query('tgl2');
        } else {
            $tgl1 = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d"))));
            $tgl2 = date('Y-m-d');
        }

        $periode = History::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
        ->where('created_at','>=',$tgl1.' 00:00:01')
        ->where('created_at','<=',$tgl2. ' 23:59:59')
        ->groupBy('date')
        ->get();

        $data_berkas_masuk = Berkas::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as jml_masuk'))
        ->where('created_at','>=',$tgl1.' 00:00:01')
        ->where('created_at','<=',$tgl2. ' 23:59:59')
        ->groupBy('date')
        ->get();

        $data_berkas_selesai = History::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as jml_selesai'))
        ->where('proses_id',8)
        ->where('created_at','>=',$tgl1.' 00:00:01')
        ->where('created_at','<=',$tgl2. ' 23:59:59')
        ->groupBy('date')
        ->get();

        $data_periode = [];
        $dat_berkas_masuk = [];
        $dat_berkas_selesai = [];

        $tot_berkas_masuk = 0;
        $tot_berkas_selesai = 0;
        foreach ($periode as $pr) {
            $data_periode[] =  date("d/m/Y", strtotime($pr->date));

            $dt_berkas_masuk = $data_berkas_masuk->where('date',$pr->date)->first();
            $dat_berkas_masuk[] = (int) ($dt_berkas_masuk ? $dt_berkas_masuk->jml_masuk : 0);

            $tot_berkas_masuk += (int) ($dt_berkas_masuk ? $dt_berkas_masuk->jml_masuk : 0);

            $dt_berkas_selesai = $data_berkas_selesai->where('date',$pr->date)->first();
            $dat_berkas_selesai[] = (int) ($dt_berkas_selesai ? $dt_berkas_selesai->jml_selesai : 0);

            $tot_berkas_selesai += (int) ($dt_berkas_selesai ? $dt_berkas_selesai->jml_selesai : 0);

        }

        $dt_pr = json_encode($data_periode);


        $data_c = [];

        $dt_chart = [];
        $dt_chart['label'] = 'Berkas Masuk ('.$tot_berkas_masuk.')';
        // $rc1 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        // $rc2 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        // $rc3 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $color = 'ffcd57';
        $dt_chart['data'] =  $dat_berkas_masuk;
        $dt_chart['backgroundColor'] = '#' . $color;
        $dt_chart['borderColor'] = '#' . $color;
        $dt_chart['borderWidth'] = 1;
        $dt_chart['color'] = 'green';
        $data_c[] = $dt_chart;

        $dt_chart = [];
        $dt_chart['label'] = 'Berkas Selesai ('.$tot_berkas_selesai.')';
        // $rc1 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        // $rc2 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        // $rc3 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $color = '36a2eb';
        $dt_chart['data'] =  $dat_berkas_selesai;
        $dt_chart['backgroundColor'] = '#' . $color;
        $dt_chart['borderColor'] = '#' . $color;
        $dt_chart['borderWidth'] = 1;
        $dt_chart['color'] = 'green';
        $data_c[] = $dt_chart;


        $dtc = json_encode($data_c);

        $berkas_selesai = Berkas::where('selesai',1)->count();
        $berkas_belum = Berkas::where('selesai',0)->count();

        

        return view('laporan.dashboard',[
            'title' => 'Dashboard',
            'periode' => $dt_pr,
            'chart' => $dtc,
            'berkas_selesai' => $berkas_selesai ? $berkas_selesai : 0,
            'berkas_belum' => $berkas_belum ? $berkas_belum : 0,

        ]);
    }


}
