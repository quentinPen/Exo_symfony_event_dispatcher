<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $subscibersIds = $container->findTaggedServiceIds('app.event_subscriber');
        $listenersIds = $container->findTaggedServiceIds('app.event_listener');
    
        $dispatcherDefinition = $container->findDefinition(EventDispatcher::class);

        foreach($subscibersIds as $id => $tagData ){
            $dispatcherDefinition->addMethodCall('addSubscriber', [
                new Reference($id)
            ]);
        }

        foreach($listenersIds as $id => $tagData ){
            foreach($tagData as $data){
                $dispatcherDefinition->addMethodCall('addListener', [
                    $data['event'],
                    [new Reference($id), $data['method']],
                    $data['priority']
                ]);
            }
        }
    }
}
