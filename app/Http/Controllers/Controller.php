<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Collection;

class Controller extends BaseController {
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * @param int $page
     * @param int $total
     * @return null|string
     */
    protected function getUrl($page, $total) {
        if ($page < 1 || $page * $this->getLimit() >= $total) {
            return null;
        }
        $params = ['page' => $page];
        if (request('limit')) {
            $params['limit'] = $this->getLimit();
        }
        return sprintf('%s?%s', request()->route()->getPath(), http_build_query($params));
    }

    /**
     * @return int
     */
    protected function getLimit() {
        return (int)request('limit', 10);
    }

    /**
     * @return int
     */
    protected function getCurrentPage() {
        return (int)request('page') ?: 1;
    }

    /**
     * @param Collection $collection
     * @param            $total
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getPagination(Collection $collection, $total) {
        $page = $this->getCurrentPage();
        return response()->json([
            'total' => $total,
            'per_page' => $this->getLimit(),
            'current_page' => $this->getCurrentPage(),
            'last_page' => floor($total / $this->getLimit()) + 1,
            'next_page_url' => $this->getUrl($page + 1, $total),
            'prev_page_url' => $this->getUrl($page - 1, $total),
            'data' => $collection
        ]);
    }

    protected function getOffset() {
        return ($this->getCurrentPage() - 1) * $this->getLimit();
    }

    protected function formatUserResponse(User $user) {
        $response = $user->toArray();
        $response['categories'] = $user->categories;

        return $response;
    }

    protected function formatCommentsResponse($comments) {
        $response = [];

        foreach ($comments as $commentObject) {
            $comment = $commentObject->toArray();
            $comment['author'] = $commentObject->user->toArray();

            $response[] = $comment;
        }

        return $response;
    }
}
