<?php
use Clockwork\Clockwork;
use li3_clockwork\extensions\StaticClockwork;
use lithium\action\Dispatcher;
use lithium\action\Controller;
use lithium\aop\Filters;

StaticClockwork::getInstance()->getTimeline()->startEvent('li3_clockwork_has_route', 'The routing has completed.');

Filters::apply(Dispatcher::class, '_callable', function ($self, $params, $chain) {
    // At this point, the routing has completed. In order to call _callable, it's routed.
    // So this is ever so slightly off actually.
    StaticClockwork::getInstance()->getTimeline()->endEvent('li3_clockwork_has_route');

    StaticClockwork::getInstance()->getTimeline()->startEvent('li3_clockwork_start_call', 'Right before the code in the controller action is executed.');

    $result = $chain->next($self, $params, $chain);

    // Now that we know whether or not the request is callable, it's going to be called.
    // This is esentially right before the code in the controller action is executed.
    // So mark the time.
    StaticClockwork::getInstance()->getTimeline()->endEvent('li3_clockwork_start_call');

    if (
        is_array($params['request']->params) &&
        array_key_exists('controller', $params['request']->params) &&
        array_key_exists('action', $params['request']->params)
    ) {
        StaticClockwork::getInstance()->getRequest()->controller = join('::', [
            $params['request']->params['controller'],
            $params['request']->params['action']
        ]);
    }

    // Requests should have special headers
    if (!stripos($params['request']->url, '__clockwork') && $result instanceof Controller) {
        $result->response->headers('X-Clockwork-Id', StaticClockwork::getInstance()->getRequest()->id, true);
        $result->response->headers('X-Clockwork-Version', Clockwork::VERSION, true);
    }

    return $result;
});

Filters::apply(Dispatcher::class, '_call', function($self, $params, $chain) {

    StaticClockwork::getInstance()->getTimeline()->startEvent('li3_clockwork_end_call', 'The controller action has been called and now a response will be returned.');

    $result = $chain->next($self, $params, $chain);

    // At this point the controller action has been called and now a response will be returned.
    // $result here contains the response and we've been setting timers all along the way...
    // The next time we'll be working with the same response is under the next filter below on
    // run() AFTER $result = $chain->next() is called... That's the end of the dispatch cycle.
    // The $result = part below is actually before this filter and the filter on _callable() above.
    StaticClockwork::getInstance()->getTimeline()->endEvent('li3_clockwork_end_call');

    return $result;
});

Filters::apply(Dispatcher::class, 'run', function($self, $params, $chain) {
    if (stripos($params['request']->url, '__clockwork')) {
        return $chain->next($self, $params, $chain);
    }

    $result = $chain->next($self, $params, $chain);

    StaticClockwork::getInstance()->resolveRequest();
    StaticClockwork::getInstance()->storeRequest();

    return $result;
});