import React from 'react';
import ReportTemplate from './ReportTemplate';

const SaldoRekeningReport = () => {
  const sampleData = [
    { id: 1, no_rekening: '1001', nama_rekening: 'Kas Kecil', saldo_awal: 5000000, debet: 2000000, kredit: 1500000, saldo_akhir: 5500000 },
    { id: 2, no_rekening: '1101', nama_rekening: 'Bank BCA', saldo_awal: 50000000, debet: 10000000, kredit: 8000000, saldo_akhir: 52000000 },
    { id: 3, no_rekening: '2001', nama_rekening: 'Hutang Usaha', saldo_awal: 15000000, debet: 5000000, kredit: 7000000, saldo_akhir: 17000000 }
  ];

  return (
    <ReportTemplate title="Laporan Saldo Rekening">
      <div className="card">
        <div className="card-header"><h3 className="card-title">Saldo Rekening</h3></div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr>
                  <th>No. Rekening</th>
                  <th>Nama Rekening</th>
                  <th>Saldo Awal</th>
                  <th>Debet</th>
                  <th>Kredit</th>
                  <th>Saldo Akhir</th>
                </tr>
              </thead>
              <tbody>
                {sampleData.map((item) => (
                  <tr key={item.id}>
                    <td>{item.no_rekening}</td>
                    <td>{item.nama_rekening}</td>
                    <td className="text-right">Rp {item.saldo_awal.toLocaleString()}</td>
                    <td className="text-right">Rp {item.debet.toLocaleString()}</td>
                    <td className="text-right">Rp {item.kredit.toLocaleString()}</td>
                    <td className="text-right font-bold">Rp {item.saldo_akhir.toLocaleString()}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </ReportTemplate>
  );
};

export default SaldoRekeningReport;
