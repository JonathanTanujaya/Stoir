import { useState } from 'react';
import InvoiceBonusList from './InvoiceBonusList';
import InvoiceBonusForm from './InvoiceBonusForm';

function InvoiceBonusPage() {
  const [selectedInvoiceBonus, setSelectedInvoiceBonus] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [refreshList, setRefreshList] = useState(false);

  const handleSave = () => {
    setSelectedInvoiceBonus(null);
    setShowForm(false);
    setRefreshList(prev => !prev);
  };

  const handleEdit = (invoiceBonus) => {
    setSelectedInvoiceBonus(invoiceBonus);
    setShowForm(true);
  };

  const handleAdd = () => {
    setSelectedInvoiceBonus(null);
    setShowForm(true);
  };

  const handleCancel = () => {
    setSelectedInvoiceBonus(null);
    setShowForm(false);
  };

  return (
    <div>
      <h1>Manajemen Invoice Bonus</h1>
      {!showForm && <button onClick={handleAdd}>Tambah Invoice Bonus Baru</button>}
      {showForm ? (
        <InvoiceBonusForm invoiceBonus={selectedInvoiceBonus} onSave={handleSave} onCancel={handleCancel} />
      ) : (
        <InvoiceBonusList onEdit={handleEdit} onRefresh={refreshList} />
      )}
    </div>
  );
}

export default InvoiceBonusPage;
