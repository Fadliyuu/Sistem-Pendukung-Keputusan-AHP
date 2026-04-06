<?php
namespace App;

use Google\Cloud\Firestore\FirestoreClient;

class Firestore {
    private ?FirestoreClient $client = null;
    private string $prefix;

    public function __construct() {
        $this->prefix = envv('FIRESTORE_PREFIX', 'ahp_');
    }

    private function client(): FirestoreClient {
        if ($this->client === null) {
            $projectId  = envv('FIRESTORE_PROJECT_ID');
            $keyFile    = envv('FIRESTORE_KEY_FILE');

            if (!$projectId) {
                throw new \RuntimeException(
                    'FIRESTORE_PROJECT_ID belum dikonfigurasi. Silakan buat file .env dari .env.example.'
                );
            }

            $config = ['projectId' => $projectId];
            if ($keyFile && file_exists($keyFile)) {
                $config['keyFilePath'] = $keyFile;
            } elseif ($keyFile) {
                // Mungkin berupa JSON string
                $decoded = json_decode($keyFile, true);
                if (is_array($decoded)) {
                    $config['keyFile'] = $decoded;
                }
            }

            $this->client = new FirestoreClient($config);
        }
        return $this->client;
    }

    public function col(string $name) {
        return $this->client()->collection($this->prefix . $name);
    }

    public function get(string $collection, string $id): ?array {
        $doc = $this->col($collection)->document($id)->snapshot();
        return $doc->exists() ? $doc->data() + ['id' => $doc->id()] : null;
    }

    public function all(string $collection): array {
        $docs = $this->col($collection)->documents();
        $out  = [];
        foreach ($docs as $d) {
            if ($d->exists()) {
                $out[] = $d->data() + ['id' => $d->id()];
            }
        }
        return $out;
    }

    public function set(string $collection, string $id, array $data): void {
        $this->col($collection)->document($id)->set($data);
    }

    public function delete(string $collection, string $id): void {
        $this->col($collection)->document($id)->delete();
    }
}
