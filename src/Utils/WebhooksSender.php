<?php


namespace App\Utils;


class WebhooksSender
{
    // The json of the object
    private $data;

    public function __construct()
    {
        $this->data = [
            'embeds' => [
                [
                    "title" => "foo",
                    "type" => "rich",
                    "description" => "lorem ipsum",
                    "color" => hexdec("3366ff"),
                    "footer" => [
                        "text" => "Infinity Cast",
                        "icon_url" => "https://upload.wikimedia.org/wikipedia/fr/d/d8/Epita.png"
                    ],
                ]
            ]
        ];
    }

    public function setDescription(string $description)
    {
        $this->data['embeds'][0]['description'] = $description;
        return $this;
    }

    public function setImage(?string $image)
    {
        if ($image) {
            $this->data['embeds'][0]['image'] = [
                "url" => $image
            ];
        }

        return $this;
    }

    public function setAuthor(string $name, string $url)
    {
        $this->data['embeds'][0]['author'] = [
            "name" => $name,
            "icon_url" => $url
        ];

        return $this;
    }


    public function send(string $webhook_url)
    {
        $timestamp = date("c", strtotime("now"));
        $this->data['embeds'][0]['timestamp'] = $timestamp;
        $json_data = json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
        //echo $response;
        curl_close($ch);
    }
}