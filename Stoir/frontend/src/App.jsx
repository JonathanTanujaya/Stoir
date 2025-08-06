import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import CustomerPage from './components/CustomerPage.jsx';
import SalesPage from './components/SalesPage.jsx';
import BarangPage from './components/BarangPage.jsx';
import KategoriPage from './components/KategoriPage.jsx';
import AreaPage from './components/AreaPage.jsx';
import SupplierPage from './components/SupplierPage.jsx';
import MBankPage from './components/MBankPage.jsx';
import MCOAPage from './components/MCOAPage.jsx';
import MDivisiPage from './components/MDivisiPage.jsx';
import MDokumenPage from './components/MDokumenPage.jsx';
import MModulePage from './components/MModulePage.jsx';
import MasterUserPage from './components/MasterUserPage.jsx';
import MResiPage from './components/MResiPage.jsx';
import MTTPage from './components/MTTPage.jsx';
import MTransPage from './components/MTransPage.jsx';
import InvoicePage from './components/InvoicePage.jsx';
import PartPenerimaanPage from './components/PartPenerimaanPage.jsx';
import ReturnSalesPage from './components/ReturnSalesPage.jsx';
import PenerimaanFinancePage from './components/PenerimaanFinancePage.jsx';
import InvoiceBonusPage from './components/InvoiceBonusPage.jsx';
import ClaimPenjualanPage from './components/ClaimPenjualanPage.jsx';
import PartPenerimaanBonusPage from './pages/PartPenerimaanBonusPage.jsx';
import ReturPenerimaanPage from './pages/ReturPenerimaanPage.jsx';
import SaldoBankPage from './pages/SaldoBankPage.jsx';
import SPVPage from './pages/SPVPage.jsx';
import StokClaimPage from './pages/StokClaimPage.jsx';
import StokMinimumPage from './pages/StokMinimumPage.jsx';
import TmpPrintTTPage from './pages/TmpPrintTTPage.jsx';
import UserModulePage from './pages/UserModulePage.jsx';
import DPaketPage from './pages/DPaketPage.jsx';
import MVoucherPage from './pages/MVoucherPage.jsx';
import MergeBarangPage from './pages/MergeBarangPage.jsx';
import OpnamePage from './pages/OpnamePage.jsx';
import './App.css';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

function App() {
  return (
    <Router>
      <div className="App">
        <nav>
          <ul>
            <li>
              <Link to="/customers">Pelanggan</Link>
            </li>
            <li>
              <Link to="/sales">Sales</Link>
            </li>
            <li>
              <Link to="/barang">Barang</Link>
            </li>
            <li>
              <Link to="/kategori">Kategori</Link>
            </li>
            <li>
              <Link to="/area">Area</Link>
            </li>
            <li>
              <Link to="/supplier">Supplier</Link>
            </li>
            <li>
              <Link to="/mbank">Bank</Link>
            </li>
            <li>
              <Link to="/mcoa">COA</Link>
            </li>
            <li>
              <Link to="/mdivisi">Divisi</Link>
            </li>
            <li>
              <Link to="/mdokumen">Dokumen</Link>
            </li>
            <li>
              <Link to="/mmodule">Modul</Link>
            </li>
            <li>
              <Link to="/master-user">Pengguna</Link>
            </li>
            <li>
              <Link to="/mresi">Resi</Link>
            </li>
            <li>
              <Link to="/mtt">TT</Link>
            </li>
            <li>
              <Link to="/mtrans">Transaksi</Link>
            </li>
            <li>
              <Link to="/invoices">Invoice</Link>
            </li>
            <li>
              <Link to="/part-penerimaan">Penerimaan Barang</Link>
            </li>
            <li>
              <Link to="/return-sales">Retur Penjualan</Link>
            </li>
            <li>
              <Link to="/penerimaan-finance">Penerimaan Finance</Link>
            </li>
            <li>
              <Link to="/invoice-bonus">Invoice Bonus</Link>
            </li>
            <li>
              <Link to="/claims">Klaim Penjualan</Link>
            </li>
            <li>
              <Link to="/part-penerimaan-bonus">Part Penerimaan Bonus</Link>
            </li>
            <li>
              <Link to="/retur-penerimaan">Retur Penerimaan</Link>
            </li>
            <li>
              <Link to="/saldo-bank">Saldo Bank</Link>
            </li>
            <li>
              <Link to="/spv">SPV</Link>
            </li>
            <li>
              <Link to="/stok-claim">Stok Claim</Link>
            </li>
            <li>
              <Link to="/stok-minimum">Stok Minimum</Link>
            </li>
            <li>
              <Link to="/tmp-print-tt">Tmp Print TT</Link>
            </li>
            <li>
              <Link to="/user-module">User Module</Link>
            </li>
            <li>
              <Link to="/d-paket">D Paket</Link>
            </li>
            <li>
              <Link to="/m-voucher">M Voucher</Link>
            </li>
            <li>
              <Link to="/merge-barang">Merge Barang</Link>
            </li>
            <li>
              <Link to="/opname">Opname</Link>
            </li>
          </ul>
        </nav>

        <Routes>
          <Route path="/customers" element={<CustomerPage />} />
          <Route path="/sales" element={<SalesPage />} />
          <Route path="/barang" element={<BarangPage />} />
          <Route path="/kategori" element={<KategoriPage />} />
          <Route path="/area" element={<AreaPage />} />
          <Route path="/supplier" element={<SupplierPage />} />
          <Route path="/mbank" element={<MBankPage />} />
          <Route path="/mcoa" element={<MCOAPage />} />
          <Route path="/mdivisi" element={<MDivisiPage />} />
          <Route path="/mdokumen" element={<MDokumenPage />} />
          <Route path="/mmodule" element={<MModulePage />} />
          <Route path="/master-user" element={<MasterUserPage />} />
          <Route path="/mresi" element={<MResiPage />} />
          <Route path="/mtt" element={<MTTPage />} />
          <Route path="/mtrans" element={<MTransPage />} />
          <Route path="/invoices" element={<InvoicePage />} />
          <Route path="/part-penerimaan" element={<PartPenerimaanPage />} />
          <Route path="/return-sales" element={<ReturnSalesPage />} />
          <Route path="/penerimaan-finance" element={<PenerimaanFinancePage />} />
          <Route path="/invoice-bonus" element={<InvoiceBonusPage />} />
          <Route path="/claims" element={<ClaimPenjualanPage />} />
          <Route path="/part-penerimaan-bonus" element={<PartPenerimaanBonusPage />} />
          <Route path="/retur-penerimaan" element={<ReturPenerimaanPage />} />
          <Route path="/saldo-bank" element={<SaldoBankPage />} />
          <Route path="/spv" element={<SPVPage />} />
          <Route path="/stok-claim" element={<StokClaimPage />} />
          <Route path="/stok-minimum" element={<StokMinimumPage />} />
          <Route path="/tmp-print-tt" element={<TmpPrintTTPage />} />
          <Route path="/user-module" element={<UserModulePage />} />
          <Route path="/d-paket" element={<DPaketPage />} />
          <Route path="/m-voucher" element={<MVoucherPage />} />
          <Route path="/merge-barang" element={<MergeBarangPage />} />
          <Route path="/opname" element={<OpnamePage />} />
          <Route path="/" element={<h1>Selamat Datang di Stoir App</h1>} />
        </Routes>
      </div>
      <ToastContainer />
    </Router>
  );
}

export default App;
