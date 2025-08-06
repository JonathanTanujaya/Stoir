import { useState } from 'react';
import AreaList from './AreaList';
import AreaForm from './AreaForm';

function AreaPage() {
  const [selectedArea, setSelectedArea] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [refreshList, setRefreshList] = useState(false);

  const handleSave = () => {
    setSelectedArea(null);
    setShowForm(false);
    setRefreshList(prev => !prev);
  };

  const handleEdit = (area) => {
    setSelectedArea(area);
    setShowForm(true);
  };

  const handleAdd = () => {
    setSelectedArea(null);
    setShowForm(true);
  };

  const handleCancel = () => {
    setSelectedArea(null);
    setShowForm(false);
  };

  return (
    <div>
      <h1>Manajemen Area</h1>
      {!showForm && <button onClick={handleAdd}>Tambah Area Baru</button>}
      {showForm ? (
        <AreaForm area={selectedArea} onSave={handleSave} onCancel={handleCancel} />
      ) : (
        <AreaList onEdit={handleEdit} onRefresh={refreshList} />
      )}
    </div>
  );
}

export default AreaPage;
