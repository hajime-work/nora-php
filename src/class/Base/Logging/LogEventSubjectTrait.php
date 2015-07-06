<?php
namespace Nora\Base\Logging;

use Nora\Base\Event;

trait logEventSubjectTrait
{
    use logSubjectTrait;
    use Event\SubjectTrait;

    /**
     * ログ処理
     *
     * @param int $level
     * @param mixed $message
     * @return void
     */
    private function log ($level, $message)
    {
        $this->fire(
            'log',
            [
                'level' => $level,
                'message' => is_String($message) ? ['msg' => $message]: $message,
                'tags' => [
                    get_class($this)
                ],
                'contect' => $this
            ]
        );
    }
}
