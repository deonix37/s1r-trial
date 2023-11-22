<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class LeadService
{
    /**
     * Создать сделку в Bitrix CRM
     * Отправить уведомление в Телеграм канал
     * Сохранить сделку в БД при успешном создании
     *
     * @param array{
     *     full_name: string,
     *     birth_date: string,
     *     phone: string,
     *     email: string,
     *     comment: string,
     * } $data
     * @return array{
     *     success: boolean,
     *     message: string,
     * }
     */
    public function createLead(array $data): array
    {
        $response = $this->addLeadToBitrixCrm($data);
        $leadStatusText = $this->getLeadStatusText($response, $data);

        $this->reportToTelegramChannel($leadStatusText);

        if (isset($response['error_description'])) {
            return [
                'success' => false,
                'message' => $response['error_description'],
            ];
        }

        Lead::create($data);

        return [
            'success' => true,
            'message' => 'Сделка успешно создана',
        ];
    }

    /**
     * @param array{
     *     full_name: string,
     *     birth_date: string,
     *     phone: string,
     *     email: string,
     *     comment: string,
     * } $data
     * @return array{result: int}|array{error_description: string}
     */
    private function addLeadToBitrixCrm(array $data): array
    {
        $nameParts = preg_split('/\s+/', $data['full_name']);

        return Http::get(config('bitrix-crm.webhook_lead_add_url'), [
            'fields[BIRTHDATE]' => $data['birth_date'],
            'fields[COMMENTS]' => $data['comment'],
            'fields[PHONE][0][VALUE]' => $data['phone'],
            'fields[EMAIL][0][VALUE]' => $data['email'],
            'fields[NAME]' => $nameParts[1],
            'fields[SECOND_NAME]' => $nameParts[2],
            'fields[LAST_NAME]' => $nameParts[0],
        ])->json();
    }

    /**
     * @param string $text
     * @return void
     */
    private function reportToTelegramChannel(string $text): void
    {
        Http::get(config('telegram.send_message_url'), [
            'chat_id' => config('telegram.lead_channel_id'),
            'text' => $text,
            'disable_web_page_preview' => true,
        ]);
    }

    /**
     * @param array $bitrixResponse
     * @param array{
     *     full_name: string,
     *     birth_date: string,
     *     phone: string,
     *     email: string,
     *     comment: string,
     * } $leadData
     * @return string
     */
    private function getLeadStatusText(
        array $bitrixResponse,
        array $leadData
    ): string
    {
        $status = isset($bitrixResponse['result']) ? (
            "Новая сделка\n" . sprintf(
                config('bitrix-crm.lead_url'),
                $bitrixResponse['result']
            )
        ) : (
            "Ошибка создания сделки\n"
            . $bitrixResponse['error_description']
        );

        return "$status\n"
            . "ФИО клиента: $leadData[full_name]\n"
            . "Дата рождения клиента: $leadData[birth_date]\n"
            . "Телефон клиента: $leadData[phone]\n"
            . "Электронная почта клиента: $leadData[email]\n"
            . "Комментарий: $leadData[comment]";
    }
}
