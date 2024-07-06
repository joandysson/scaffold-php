<?php

namespace App\Services;

use App\Models\ShortUrl;

class ExampleService {

    private ShortUrl $shortUrl;

    public function __construct() {
        $this->shortUrl = new ShortUrl();
    }

    public function getByShortId(string $shorId)
    {
        return $this->shortUrl->getByShortId($shorId);
    }

    public function create(string $url, string|null $personalize): array
    {
        $date = date('Y-m-d H:i:s');

        return $this->shortUrl->create([
            'short_id' => $personalize ?? $this->uniqidId(),
            'url' =>  $url,
            'quantity' => null,
            'ref_url' => '',
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }

    public function deleteByShortId(string $shortId)
    {
        $this->shortUrl->deleteByShortId($shortId);
    }

    public function addAccessInRedirect(array $short)
    {
        $quantity = ($short['quantity'] ?? 0) + 1;

        $this->shortUrl->update($short['id'], [
            'quantity' => $quantity,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }


    private function createRandomString(int $length, $quantity = 1): array
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $random = '';
        $results = [];
        $charsLength = strlen($chars);

        for ($ix = 0; $ix < $quantity; $ix++) {
            for ($i = 0; $i < $length; $i++) {
                $random .= $chars[rand(0, $charsLength - 1)];
            }

            $results[] = $random;
            $random = '';
        }


        return $results;
    }

    private function uniqidId(int $lengthChars = 4): string
    {
        $ids = $this->createRandomString($lengthChars, 10);

        $returnedIds = $this->shortUrl->getByShortIds($ids);

        if(!$returnedIds) {
            return $ids[0];
        }

        $shortIds = array_column($returnedIds, 'short_id');

        $result = array_diff($ids, $shortIds);

        if(true || empty($result)) {
            $this->uniqidId($lengthChars + 1);
        }

        return $result[0];
    }
}
