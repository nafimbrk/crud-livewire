<?php

namespace App\Livewire;

use App\Models\Employee as ModelsEmployee;
use Livewire\Component;
use Livewire\WithPagination;

class Employee extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $nama;
    public $email;
    public $alamat;
    public $updateData = false;
    public $employe_id;
    public $katakunci;
    public $employee_selected_id = [];
    public $sortColumn = 'nama';
    public $sortDirection = 'asc';

    public function store() {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required'
        ];
        $pesan = [
            'nama.required' => 'nama wajib diisi',
            'email.required' => 'email wajib diisi',
            'email.email' => 'format email tidak sesuai',
            'alamat.required' => 'alamat wajib diisi'
        ];

        $validated = $this->validate($rules, $pesan);
        ModelsEmployee::create($validated);
        session()->flash('message', 'data berhasil ditambahkan');

        $this->clear();
    }

    public function edit($id) {
        $data = ModelsEmployee::find($id);
        $this->nama = $data->nama;
        $this->email = $data->email;
        $this->alamat = $data->alamat;

        $this->updateData = true;
        $this->employe_id = $id;
    }

    public function update() {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required'
        ];
        $pesan = [
            'nama.required' => 'nama wajib diisi',
            'email.required' => 'email wajib diisi',
            'email.email' => 'format email tidak sesuai',
            'alamat.required' => 'alamat wajib diisi'
        ];

        $validated = $this->validate($rules, $pesan);
        $data = ModelsEmployee::find($this->employe_id);
        $data->update($validated);
        session()->flash('message', 'data berhasil diupdate');

        $this->clear();
    }

    public function clear() {
        $this->nama = '';
        $this->email = '';
        $this->alamat = '';

        $this->updateData = false;
        $this->employe_id = '';
        $this->employee_selected_id = [];
    }

    public function delete() {
        if ($this->employe_id != '') {
            $id = $this->employe_id;
            ModelsEmployee::find($id)->delete();
        }
        if (count($this->employee_selected_id)) {
            for ($x = 0; $x < count($this->employee_selected_id); $x++) { 
                ModelsEmployee::find($this->employee_selected_id[$x])->delete();            }
        }
        session()->flash('message', 'data berhasil dihapus');
        $this->clear();
    }

    public function delete_confirmation($id) {
        if ($id != '') {
            $this->employe_id = $id;
        }
    }

    public function sort($columnName) {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function render()
    {
        if($this->katakunci != null) {
            $data = ModelsEmployee::where('nama', 'like', '%' . $this->katakunci . '%')
            ->orWhere('email', 'like', '%' . $this->katakunci . '%')
            ->orWhere('alamat', 'like', '%' . $this->katakunci . '%')
            ->orderBy($this->sortColumn, $this->sortDirection)->paginate(2);
        } else {
            $data = ModelsEmployee::orderBy($this->sortColumn, $this->sortDirection)->paginate(2);
        }
        return view('livewire.employee', ['dataEmployees' => $data] );
    }
}
