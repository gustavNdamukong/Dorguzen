<?php

namespace Dorguzen\Modules\Testimonials\Services;

use Dorguzen\Modules\Testimonials\Models\Testimonials;

class TestimonialsService
{
    public function __construct(
        private Testimonials $model,
    ) {}

    // -------------------------------------------------------------------------
    // Payload builders
    // -------------------------------------------------------------------------

    public function homePagePayload(int $limit = 3): array
    {
        $rows = $this->model->getApproved($limit);

        foreach ($rows as &$row) {
            $row['stars_html'] = $this->buildStarsHtml((int) $row['testimonial_rating']);
        }
        unset($row);

        return ['testimonials' => $rows];
    }

    public function publicPagePayload(): array
    {
        $rows = $this->model->getApproved();

        foreach ($rows as &$row) {
            $row['stars_html'] = $this->buildStarsHtml((int) $row['testimonial_rating']);
        }
        unset($row);

        return ['testimonials' => $rows];
    }

    public function adminPayload(): array
    {
        $rows = $this->model->getAllForAdmin();

        foreach ($rows as &$row) {
            $row['stars_html']   = $this->buildStarsHtml((int) $row['testimonial_rating']);
            $row['status_class'] = match ($row['testimonial_status']) {
                'approved' => 'success',
                'rejected' => 'danger',
                default    => 'warning',
            };
        }
        unset($row);

        return [
            'testimonials' => $rows,
            'total'        => count($rows),
            'pendingCount' => count(array_filter($rows, fn($r) => $r['testimonial_status'] === 'pending')),
        ];
    }

    // -------------------------------------------------------------------------
    // Mutations
    // -------------------------------------------------------------------------

    public function submit(array $post): array
    {
        $name    = trim(strip_tags(htmlspecialchars($post['testimonial_name']    ?? '', ENT_QUOTES, 'UTF-8')));
        $comment = trim(strip_tags(htmlspecialchars($post['testimonial_comment'] ?? '', ENT_QUOTES, 'UTF-8')));
        $rating  = (int) ($post['testimonial_rating'] ?? 0);

        if ($name === '') {
            return ['ok' => false, 'error' => 'Your name is required.'];
        }

        if ($comment === '') {
            return ['ok' => false, 'error' => 'A comment is required.'];
        }

        if ($rating < 1 || $rating > 5) {
            return ['ok' => false, 'error' => 'Please select a rating between 1 and 5 stars.'];
        }

        $company = trim(strip_tags(htmlspecialchars($post['testimonial_company'] ?? '', ENT_QUOTES, 'UTF-8')));
        $role    = trim(strip_tags(htmlspecialchars($post['testimonial_role']    ?? '', ENT_QUOTES, 'UTF-8')));
        $email   = trim($post['testimonial_email'] ?? '');

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Please enter a valid email address, or leave it blank.'];
        }

        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        $record = container(Testimonials::class);
        $record->testimonial_name    = $name;
        $record->testimonial_company = $company !== '' ? $company : null;
        $record->testimonial_role    = $role    !== '' ? $role    : null;
        $record->testimonial_email   = $email   !== '' ? $email   : null;
        $record->testimonial_rating  = $rating;
        $record->testimonial_comment = $comment;
        $record->testimonial_status  = 'pending';
        $record->save();

        return ['ok' => true, 'error' => ''];
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['approved', 'rejected', 'pending'], true)) {
            return false;
        }

        $rows = $this->model->updateObject(
            ['testimonial_status' => $status],
            ['testimonial_id' => $id]
        );

        return $rows !== false;
    }

    public function delete(int $id): bool
    {
        $rows = $this->model->deleteWhere(['testimonial_id' => $id]);
        return $rows !== false;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function buildStarsHtml(int $rating): string
    {
        $rating = max(1, min(5, $rating));
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }
}
