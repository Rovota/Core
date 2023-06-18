<?php

use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\Interfaces\Solution;

/** @var $throwable Throwable * */
/** @var $request array * */
/** @var $traces array * */
/** @var $snippet array * */
/** @var $unhandled bool * */

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<title><?= isset($throwable) ? $throwable::class : 'Unknown Exception' ?></title>
	<meta name="theme-color" content="#F6F6F6">
	<meta name="color-scheme" content="light dark">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1, viewport-fit=cover">
	<link rel="stylesheet" href="/vendor/Rovota/core/src/Web/assets/styles/theming.css">
	<link rel="stylesheet" href="/vendor/Rovota/core/src/Web/assets/styles/debug.css">
</head>
<body class="theme-automatic accent-default">

<container>

	<nav>
		<ul>
			<li><?= $request['full_url'] ?? 'Unknown URL' ?></li>
			<li><a href="https://rovota.gitbook.io/core" target="_blank" rel="noreferrer">Documentation</a></li>
		</ul>
	</nav>

	<header>

		<card class="throwable">
			<div class="name">
				<?php
				if ($unhandled === true) { ?>
					<span class="unhandled">Unhandled</span>
				<?php
				} ?>
				<span><?= isset($throwable) ? $throwable::class : 'Unknown Exception' ?></span>
			</div>
			<h1><?= isset($throwable) ? $throwable->getMessage() : 'There is no message available' ?></h1>
			<hr>
			<p>
				<span>PHP <?= PHP_VERSION ?></span>
				<span>Core <?= Application::$version->full() ?></span>
			</p>
		</card>

		<?php
		if (isset($solution) && $solution instanceof Solution) { ?>
			<card class="solution">
				<p><b><?= str_replace('\\', '\\<wbr>', htmlentities($solution->getTitle())) ?></b></p>
				<p><?= $solution->getDescription() ?></p>
				<?php
				foreach ($solution->getDocumentationLinks() as $link_title => $link_url) {
					echo sprintf('<p><a href="%s" class="accent-neutral">%s</a></p>', $link_url, $link_title);
				}
				?>
			</card>
		<?php
		} ?>

	</header>

	<main>

		<card class="stack">

			<traces>
				<heading>
					<span>Stack Trace</span>
				</heading>
				<content>
				<?php
				foreach ($traces as $trace) { ?>
					<trace>
						<span><?= str_replace('\\', '\\<wbr>', $trace['class'] ?? $trace['file']) ?><small>:<?= $trace['line'] ?></small></span>
						<span><b><?= $trace['type'].$trace['function'] ?></b></span>
					</trace>
				<?php
				} ?>
				</content>
			</traces>

			<preview>
				<heading>
					<span><?= str_replace('\\', '\\<wbr>', $throwable->getFile()) ?><small>:<?= $throwable->getLine() ?></small></span>
				</heading>
				<file>
					<table>
						<tr class="empty">
							<td></td>
							<td></td>
						</tr>
						<?php
						foreach ($snippet as $number => $content) {
							$number++;
							$class = '';
							if ($number === $throwable->getLine()) {
								$class = 'highlight';
							}
							if (str_starts_with(trim($content), '//')) {
								$class = 'comment';
							}
							if ($number < ($throwable->getLine() - 10)) {
								continue;
							}
							if ($number > ($throwable->getLine() + 10)) {
								break;
							}
							?>
							<tr class="<?= $class ?>">
								<td><?= $number ?></td>
								<td>
									<pre><code><?= htmlentities(str_replace('	', '    ', $content), encoding: 'UTF-8') ?></code></pre>
								</td>
							</tr>
						<?php
						} ?>
						<tr class='empty'>
							<td></td>
							<td></td>
						</tr>
					</table>
				</file>
			</preview>

		</card>

	</main>

</container>

</body>
</html>