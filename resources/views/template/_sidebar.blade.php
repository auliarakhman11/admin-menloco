<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('img') }}/MEN_LOCO_BLACK.png" alt="" width="120" viewBox="0 0 25 42"
                    version="1.1">



            </span>

            {{-- <span class="demo menu-text fw-bolder ms-2">Men Loco</span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        {{-- <li class="menu-item active">
      <a href="{{ route('penilaian') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bxs-dashboard"></i>
        <div data-i18n="Analytics">Penilaian</div>
      </a>
    </li> --}}




        <li class="menu-header small text-uppercase">
            <span class="menu-header-text"><i class='bx bxs-user-pin'></i> {{ Auth::user()->name }}</span>
        </li>


        @if (Auth::user()->role_id == 1)
            <li class="menu-item {{ Request::is(['/', 'laporanRefund']) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-store'></i>
                    <div data-i18n="Penjualan">Penjualan</div>
                </a>
                <ul class="menu-sub">

                    <li class="menu-item {{ Request::is('/') ? 'active' : '' }}">
                        <a href="{{ route('laporanPenjualan') }}" class="menu-link">
                            <div data-i18n="Laporan Penjualan">Laporan Penjualan</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Request::is('laporanRefund') ? 'active' : '' }}">
                        <a href="{{ route('laporanRefund') }}" class="menu-link">
                            <div data-i18n="Laporan Penjualan">Laporan Refund</div>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="menu-item {{ Request::is(['pengeluaran']) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-calculator'></i>
                    <div data-i18n="Accounting">Accounting</div>
                </a>
                <ul class="menu-sub">

                    <li class="menu-item {{ Request::is('pengeluaran') ? 'active' : '' }}">
                        <a href="{{ route('pengeluaran') }}" class="menu-link">
                            <div data-i18n="Laporan Pengeluaran">Laporan Pengeluaran</div>
                        </a>
                    </li>


                </ul>
            </li>
            <li class="menu-item {{ Request::is(['user', 'service', 'karyawan', 'diskon']) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bxs-book-content'></i>
                    <div data-i18n="Data Master">Data Master</div>
                </a>
                <ul class="menu-sub">


                    <li class="menu-item {{ Request::is('user') ? 'active' : '' }}">
                        <a href="{{ route('user') }}" class="menu-link">
                            <div data-i18n="User">User</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Request::is('service') ? 'active' : '' }}">
                        <a href="{{ route('service') }}" class="menu-link">
                            <div data-i18n="Service">Service</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Request::is('karyawan') ? 'active' : '' }}">
                        <a href="{{ route('karyawan') }}" class="menu-link">
                            <div data-i18n="Karyawan">Karyawan</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Request::is('diskon') ? 'active' : '' }}">
                        <a href="{{ route('diskon') }}" class="menu-link">
                            <div data-i18n="diskon">Diskon</div>
                        </a>
                    </li>

                </ul>
            </li>
        @endif






        {{-- <li class="menu-item {{ Request::is(['input-stok-masuk','checker-masuk','list-stok-masuk']) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bxs-package"></i>
        <div data-i18n="Penerimaan">Penerimaan</div>
      </a>
      <ul class="menu-sub">

        <li class="menu-item {{ Request::is('input-stok-masuk') ? 'active' : '' }}">
          <a href="{{ route('inputStokMasuk') }}" class="menu-link">
            <div data-i18n="Input Stok Masuk">Input Stok Masuk</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('checker-masuk') ? 'active' : '' }}">
          <a href="{{ route('checkerMasuk') }}" class="menu-link">
            <div data-i18n="Checker">Checker Penerimaan</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('list-stok-masuk') ? 'active' : '' }}">
          <a href="{{ route('listStokMasuk') }}" class="menu-link">
            <div data-i18n="Checker">List Stok Masuk</div>
          </a>
        </li>




      </ul>
    </li>

    <li class="menu-item {{ Request::is(['data-block','data-stok','return-barang','list-stok-hold']) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bxs-component"></i>
        <i class=''></i>
        <div data-i18n="Penyimpanan">Penyimpanan</div>
      </a>
      <ul class="menu-sub">

        <li class="menu-item {{ Request::is('data-block') ? 'active' : '' }}">
          <a href="{{ route('dataBlock') }}" class="menu-link">
            <div data-i18n="Data Layout">Data Layout</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('data-stok') ? 'active' : '' }}">
          <a href="{{ route('dataStok') }}" class="menu-link">
            <div data-i18n="Data Stok">Data Stok</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('return-barang') ? 'active' : '' }}">
          <a href="{{ route('returnBarang') }}" class="menu-link">
            <div data-i18n="Return Barang">Input Barang Hold</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('list-stok-hold') ? 'active' : '' }}">
          <a href="{{ route('listStokHold') }}" class="menu-link">
            <div data-i18n="Return Barang">List Barang Hold</div>
          </a>
        </li>



      </ul>
    </li>

    <li class="menu-item {{ Request::is(['input-stok-keluar','checker-keluar','list-stok-keluar']) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bxs-truck"></i>
        <div data-i18n="Pendistribusian">Pendistribusian</div>
      </a>
      <ul class="menu-sub">

        <li class="menu-item {{ Request::is('input-stok-keluar') ? 'active' : '' }}">
          <a href="{{ route('inputStokKeluar') }}" class="menu-link">
            <div data-i18n="Input Stok Keluar">Input Stok Keluar</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('checker-keluar') ? 'active' : '' }}">
          <a href="{{ route('checkerKeluar') }}" class="menu-link">
            <div data-i18n="Checker">Checker Pendistribusian</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('list-stok-keluar') ? 'active' : '' }}">
          <a href="{{ route('listStokKeluar') }}" class="menu-link">
            <div data-i18n="Checker">List Stok Keluar</div>
          </a>
        </li>


      </ul>
    </li>

    <li class="menu-item {{ Request::is(['laporan-stok-masuk','laporan-stok-keluar','laporan-penerimaan-pengiriman','laporan-stok-kadaluarsa']) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bxs-report"></i>
        <div data-i18n="Laporan">Laporan</div>
      </a>
      <ul class="menu-sub">

        <li class="menu-item {{ Request::is('laporan-stok-masuk') ? 'active' : '' }}">
          <a href="{{ route('laporanStokMasuk') }}" class="menu-link">
            <div data-i18n="Laporan Stok Masuk">Laporan Stok Masuk</div>
          </a>
        </li>
        <li class="menu-item {{ Request::is('laporan-stok-keluar') ? 'active' : '' }}">
          <a href="{{ route('laporanStokKeluar') }}" class="menu-link">
            <div data-i18n="Laporan Stok Keluar">Laporan Stok Keluar</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('laporan-penerimaan-pengiriman') ? 'active' : '' }}">
          <a href="{{ route('laporanPenerimaanPengiriman') }}" class="menu-link">
            <div data-i18n="Laporan Penerimaan dan Pengiriman">Laporan Penerimaan dan Pengiriman</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('laporan-stok-kadaluarsa') ? 'active' : '' }}">
          <a href="{{ route('laporanStokKadaluarsa') }}" class="menu-link">
            <div data-i18n="Laporan Stok Kadaluarsa">Laporan Stok Kadaluarsa</div>
          </a>
        </li>

        


      </ul>
    </li>

    <li class="menu-item {{ Request::is(['user','barang','mitra']) ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bxs-data"></i>
        <div data-i18n="Data Master">Data Master</div>
      </a>
      <ul class="menu-sub">

        <li class="menu-item {{ Request::is('barang') ? 'active' : '' }}">
          <a href="{{ route('barang') }}" class="menu-link">
            <div data-i18n="Data Barang">Data Barang</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('user') ? 'active' : '' }}">
          <a href="{{ route('user') }}" class="menu-link">
            <div data-i18n="Data User">Data User</div>
          </a>
        </li>

        <li class="menu-item {{ Request::is('mitra') ? 'active' : '' }}">
          <a href="{{ route('mitra') }}" class="menu-link">
            <div data-i18n="Data mitra">Data mitra</div>
          </a>
        </li>

      </ul>
    </li> --}}



        <li class="menu-item">
            <a href="{{ route('gantiPassword') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-key'></i>
                <div data-i18n="Analytics">Ganti Password</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('logout') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-log-out-circle'></i>
                <div data-i18n="Analytics">Logout</div>
            </a>
        </li>








    </ul>
</aside>
