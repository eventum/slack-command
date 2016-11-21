<?php

namespace App\SlashCommandHandlers;

use App\EventumApi;
use DateTime;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\AttachmentField;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;

class IssueDetails extends SignatureHandler
{
    public $signature = "* view {issue_id}";

    public function handle(Request $request): Response
    {
        $issue_id = (int)$this->getArgument('issue_id');
        if (!$issue_id) {
            return $this->respondToSlack("You must provide numeric issue_id");
        }

        $data = (new EventumApi())->getIssueDetails($issue_id);

        $attachmentFields = array(
            AttachmentField::create('Reported by', $data['reporter'])->displaySideBySide(),
            AttachmentField::create('Priority', $data['pri_title'])->displaySideBySide(),
            AttachmentField::create('Assignment', $data['assignments'])->displaySideBySide(),
            AttachmentField::create('Status', $data['sta_title'])->displaySideBySide(),
            AttachmentField::create('Last update', $data['iss_last_action_date'])->displaySideBySide(),
        );

        return $this->respondToSlack($data['iss_original_description'])
            ->withAttachment(Attachment::create()
                ->setColor('#006486')
                ->setTitle("{$data['prc_title']} #{$issue_id} : {$data['iss_summary']}")
                ->setTitleLink($data['iss_issue_link'])
                ->setFallback("{$data['iss_summary']}\n{$data['sta_title']}\nAssigned to {$data['assignments']}")
                ->setFooter("Created by {$data['reporter']}")
                ->setTimestamp(new DateTime("@{$data['iss_created_date_ts']}"))
                ->setFields($attachmentFields)
            );
    }
}