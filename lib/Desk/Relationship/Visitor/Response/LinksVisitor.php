<?php

namespace Desk\Relationship\Visitor\Response;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;
use Desk\Relationship\Resource\CommandBuilder;
use Desk\Relationship\Resource\CommandBuilderInterface;

/**
 * Processes link parameters into commands representing the link
 *
 * This response visitor parses the _links element in the response, and
 * creates command objects that will retrieve the linked resource.
 */
class LinksVisitor extends AbstractVisitor
{

    /**
     * Builds commands from links
     *
     * @var \Desk\Relationship\Resource\CommandBuilderInterface
     */
    private $builder;


    /**
     * Accepts a CommandBuilderInterface object, used to build commands
     *
     * @param \Desk\Relationship\Resource\CommandBuilderInterface $builder
     */
    public function __construct(CommandBuilderInterface $builder = null)
    {
        $this->builder = $builder ?: new CommandBuilder();
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldName()
    {
        return 'links';
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceFromData(CommandInterface $command, Parameter $structure, array $data)
    {
        // check if there's a link provided for the param's "wire" name
        $links = $this->get($command, 'links');
        if (!empty($links[$param->getWireName()])) {
            // if links is a multidimensional array; iterate
            if (isset($links[$param->getWireName()][0])) {
                $value[self::ELEMENT][$param->getName()] = array();

                foreach ($links[$param->getWireName()] as $field) {
                    $linkCommand = $this->builder->createLinkCommand(
                        $command,
                        $param,
                        $field
                    );

                    $value[self::ELEMENT][$param->getName()][] = $linkCommand;
                }
            } else {
                $linkCommand = $this->builder->createLinkCommand(
                    $command,
                    $param,
                    $links[$param->getWireName()]
                );

                $value[self::ELEMENT][$param->getName()] = $linkCommand;
            }
        }
    }
}
