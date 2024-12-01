<?php
namespace SimpleWire;

class SimpleWire
{
    private $state = [];
    private $storageFile;

    public function __construct()
    {
        $this->storageFile = __DIR__ . '/../storage/wire-state.json';
        $this->loadState();
    }

    private function loadState(): void
    {
        if (file_exists($this->storageFile)) {
            $content = file_get_contents($this->storageFile);
            if ($content !== false) {
                $this->state = json_decode($content, true) ?? [];
            }
        }
    }

    private function saveState(): void
    {
        $storageDir = dirname($this->storageFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
        file_put_contents($this->storageFile, json_encode($this->state));
    }

    public function setState(string $key, $value): void
    {
        $this->state[$key] = $value;
        $this->saveState();
    }


    public function getState(string $key)
    {
        return $this->state[$key] ?? null;
    }

    public function render(string $component): string
    {
        ob_start();
        $wire = $this;
        require __DIR__ . "/../components/{$component}.php";
        return ob_get_clean();
    }
}