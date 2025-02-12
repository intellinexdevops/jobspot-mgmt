<?php

namespace App\Utils;

class Avatar
{

    private $avatar = [
        "upload/20250131005048.png",
        "upload/20250131005924.png",
        "upload/20250131005959.png",
        "upload/20250131010236.png",
        "upload/20250131010257.png",
    ];

    public function getAllAvatar(): array
    {
        return $this->avatar;
    }

    public function getRandomAvatar(): string
    {
        return $this->avatar[array_rand($this->avatar)];
    }
}
