<?php
namespace EthereumRawTx\Abi;

class EventItem extends AbstractItem
{
    public function __construct(array $item)
    {
        parent::__construct($item);
    }

    public function parseInputs(string $hexData, array $topics)
    {
        $result = [];
        $position = 0;

        // ignore first topic which is event name
        array_shift($topics);

        foreach ($this->getInputs() as $i => $input) {

            if($input->isIndexed()) {
                $topicRaw = array_shift($topics);
                if(null === $topicRaw) {
                    throw new \Exception("Missing topic");
                }
                //$result [$input['name']] = $this->decodeParam($input->getType(), $topicRaw, $i * 64);
                $result [$input->getName()] = $input->parse($topicRaw);
            }
            else {
                $result [$input->getName()] = $input->parse($hexData, $position);
            }
        }

        return $result;
    }
}