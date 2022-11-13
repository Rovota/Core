<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Handlers;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Rovota\Core\Http\Client\Client;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Text;
use Throwable;

class DiscordHandler extends AbstractProcessingHandler
{

	private bool $initialized = false;
	private Client $client;

	private string $url;

	// -----------------

	public function __construct(string $token, string $channel, int|string|Level $level = Level::Debug, bool $bubble = true)
	{
		$this->url = sprintf('https://discord.com/api/webhooks/%s/%s', $channel, $token);
		parent::__construct($level, $bubble);
	}

	// -----------------

	protected function write(LogRecord $record): void
	{
		if ($this->initialized === false) {
			$this->initialize();
		}

		try {
			$this->client->post($this->url)->json([
				'content' => moment($record->datetime)->toRfc3339String(),
				'embeds' => [$this->createEmbed($record)],
			])->execute();
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
	}

	// -----------------

	private function initialize(): void
	{
		$this->client = new Client();
	}

	private function createEmbed(LogRecord $record): array
	{
		$parameters = [
			'title' => $record->level->name,
			'type' => 'rich',
			'description' => $record->message,
			'timestamp' => $record->datetime->format('c'),
			'color' => hexdec($this->getColorForLevel($record->level)),
			'footer' => [
				'text' => Text::pascal(Application::getEnvironment()),
			],
		];
		
		foreach ($record->context as $key => $value) {
			$parameters['fields'][] = [
				'name' => $key,
				'value' => $value,
				'inline' => true,
			];
		}

		return $parameters;
	}

	private function getColorForLevel(Level $level): string
	{
		return match($level->name) {
			'Debug' => '666666',
			'Info' => '37B8E1',
			'Notice' => '0D89CF',
			'Warning' => 'F6902D',
			'Error' => 'E54646',
			'Critical' => 'D5351F',
			'Alert' => '08B5AA',
			'Emergency' => 'C566FF',
			default => 'F1F1F1',
		};
	}

}