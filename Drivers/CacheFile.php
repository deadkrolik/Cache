<?php
declare(strict_types=1);

class CacheFile implements CacheInterface
{
    /**
     * @var string $path Путь к файлу
     */
    private string $path;

    /**
     * Конструктор
     * @param string $path Путь к файлу
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): void
    {
        $data = $this->read();
        $data[$key] = $value;
        $this->write($data);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        $data = $this->read();
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): void
    {
        $data = $this->read();
        if (array_key_exists($key, $data)) {
            unset($data[$key]);
        }
        $this->write($data);
    }

    /**
     * Проверяет файл на существование и получает десериализованное значение
     * @return десериализованный массив
     */
    private function read(): array
    {
        if (!is_readable($this->path)) {
            throw new RuntimeException();
        }

        $content = file_get_contents($this->path);
        $unserialized = unserialize($content);
        if (!is_array($unserialized)) {
            $unserialized = [];
        }

        return $unserialized;
    }

    /**
     * Проверяет файл на существование и пишет сериализованное значение
     * @param  array $data Содержит значение для записи
     */
    private function write(array $data): void
    {
        if (!is_readable($this->path)) {
            throw new RuntimeException();
        }

        $serialized = serialize($data);
        $newData = file_put_contents($this->path, $serialized);
        
        if ($newData === false) {
            throw new RuntimeException();
        }
    }
}
