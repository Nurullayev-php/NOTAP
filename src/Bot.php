<?php

declare(strict_types=1);

use GuzzleHttp\Client;

class Bot
{
    public string $tgApi;
    public Client $client;
    public Note $note;

    public function __construct(string $token)
    {
        $this->tgApi = "https://api.telegram.org/bot$token/";
        $this->client = new Client(['base_uri' => $this->tgApi]);
        $this->note = new Note();
    }

    public function sendMessage(int $chatId, string $message, $replyMarkup = null): void
    {
        $this->client->post('sendMessage',[
            'form_params'=>[
                'chat_id'=>$chatId,
                'text'=>$message,
                'reply_markup'=>$replyMarkup
            ]
        ]);
    }

    public function editMessageText(int $chatId, int $messageId, string $message, $replyMarkup = null): void
    {
        $this->client->post('editMessageText',[
            'form_params'=>[
                'chat_id'=>$chatId,
                'message_id'=>$messageId,
                'text'=>$message,
                'reply_markup'=>$replyMarkup
            ]
        ]);
    }


    public function hendleStarCommand(int $chatId, string $userName): void
    {
        $message = "Hello @$userName. Welcome to NOTE telegram bot.";

        $keyboard = json_encode([
            'inline_keyboard'=>[
                [
                    ['text' => 'Add note', 'callback_data' => '/add']
                ]
            ]
        ]);

        $this->sendMessage($chatId, $message, $keyboard);
    }

    public function hendleAddCommand(int $chatId): void
    {
        $message = "Please, enter a task.";

        $this->sendMessage($chatId, $message);
    }

    public function addNote(int $chatId, string $text): void
    {
        $this->note->add($text, $chatId);

        $message = "The Note successfully added.";

        $keyboard = json_encode([
            'inline_keyboard'=>[
                [
                    ['text' => 'Add task', 'callback_data' => '/add'],
                    ['text' => 'Show tasks', 'callback_data' => '/show']
                ]
            ]
        ]);

        $this->sendMessage($chatId, $message, $keyboard);
    }

    public function showAllTasks(int $chatId, int $messageId = null, string $act = null): void
    {
        $tasksList = $this->note->getOneUserId($chatId);
        $keyboard = ['inline_keyboard'=>[]];

        if (count($tasksList)){
            foreach ($tasksList as $task){
                $dot = str_repeat(".", 100);
                $text = "{$task['text']} $dot";
                $keyboard['inline_keyboard'][] = [['text' => "$text", 'callback_data' => $task['id']]];
            }

            $keyboard['inline_keyboard'][] = [['text' => 'Add task', 'callback_data' => '/add']];

            $keyboards = json_encode($keyboard);
            $message = $act . "Your notes\n";

            if (is_numeric($messageId)){
                $this->editMessageText($chatId, $messageId, $message, $keyboards);
                return;
            }
            $this->sendMessage($chatId, $message, $keyboards);

        } else {
            $message = "Sorry, no notes were found.";
            $keyboard = json_encode([
                'inline_keyboard'=>[
                    [
                        ['text' => 'Add task', 'callback_data' => '/add']
                    ]
                ]
            ]);

            $this->sendMessage($chatId, $message, $keyboard);
        }
    }
}