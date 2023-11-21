<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class LeadService
{
    public function createLead(array $data): array
    {
        $response = $this->addLeadToBitrixCrm($data);

        if (isset($response['error'])) {
            $this->reportLeadErrorToTelegramChannel($response, $data);

            return [
                'success' => false,
                'message' => $response['error_description'],
            ];
        }

        Lead::create($data);

        $this->reportLeadCreatedToTelegramChannel($response, $data);

        return [
            'success' => true,
            'message' => 'Сделка успешно создана',
        ];
    }

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

    private function reportLeadCreatedToTelegramChannel(
        array $bitrixResponse,
        array $leadData
    ): void
    {
        $this->reportToTelegramChannel(
            "Новая сделка\n"
            . sprintf(
                config('bitrix-crm.lead_url'),
                $bitrixResponse['result']
            )
            . "\n"
            . $this->formatLeadData($leadData)
        );
    }

    private function reportLeadErrorToTelegramChannel(
        array $bitrixResponse,
        array $leadData
    ): void
    {
        $this->reportToTelegramChannel(
            "Ошибка создания сделки\n"
            . $bitrixResponse['error_description']
            . "\n"
            . $this->formatLeadData($leadData)
        );
    }

    private function reportToTelegramChannel(string $text): void
    {
        Http::get(config('telegram.send_message_url'), [
            'chat_id' => config('telegram.lead_channel_id'),
            'text' => $text,
        ]);
    }

    private function formatLeadData(array $data): string
    {
        return "ФИО клиента: $data[full_name]\n"
            . "Дата рождения клиента: $data[birth_date]\n"
            . "Телефон клиента: $data[phone]\n"
            . "Электронная почта клиента: $data[email]\n"
            . "Комментарий: $data[comment]";
    }
}
