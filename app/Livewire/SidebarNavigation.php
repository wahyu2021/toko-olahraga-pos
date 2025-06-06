<?php

namespace App\Livewire; // Pastikan namespace ini benar (App\Livewire untuk Laravel 10/11)

use Livewire\Component;

class SidebarNavigation extends Component
{
    public bool $sidebarOpen = true;

    public function mount()
    {
        // Mengirim event dengan data menggunakan named arguments (Livewire v3)
        $this->dispatch('sidebar-state-changed', open: $this->sidebarOpen);
    }

    public function toggleSidebar()
    {
        $this->sidebarOpen = !$this->sidebarOpen;
        // Mengirim event dengan data menggunakan named arguments (Livewire v3)
        $this->dispatch('sidebar-state-changed', open: $this->sidebarOpen);
    }

    public function closeSidebar()
    {
        if ($this->sidebarOpen) { // Hanya dispatch jika ada perubahan state
            $this->sidebarOpen = false;
            // Mengirim event dengan data menggunakan named arguments (Livewire v3)
            $this->dispatch('sidebar-state-changed', open: $this->sidebarOpen);
        }
    }

    public function render()
    
    {
        return view('livewire.sidebar-navigation');
    }
}