<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum StatusCode: int
{
	use EnumHelpers;

	// 1xx
	case Continue = 100;
	case SwitchingProtocols = 101;
	case Processing = 102;
	case EarlyHints = 103;

	// 2xx
	case Ok = 200;
	case Created = 201;
	case Accepted = 202;
	case NoContent = 204;
	case ResetContent = 205;
	case PartialContent = 206;
	case MultiStatus = 207;
	case AlreadyReported = 208;
	case IAmUsed = 226;

	// 3xx
	case MultipleChoices = 300;
	case MovedPermanently = 301;
	case Found = 302;
	case SeeOther = 303;
	case NotModified = 304;
	case TemporaryRedirect = 307;
	case PermanentRedirect = 308;

	// 4xx
	case BadRequest = 400;
	case Unauthorized = 401;
	case PaymentRequired = 402;
	case Forbidden = 403;
	case NotFound = 404;
	case MethodNotAllowed = 405;
	case NotAcceptable = 406;
	case ProxyAuthenticationRequired = 407;
	case RequestTimeout = 408;
	case Conflict = 409;
	case Gone = 410;
	case LengthRequired = 411;
	case PreconditionFailed = 412;
	case PayloadTooLarge = 413;
	case UriTooLong = 414;
	case UnsupportedMediaType = 415;
	case RangeNotSatisfiable = 416;
	case ExpectationFailed = 417;
	case Teapot = 418;
	case MisdirectedRequest = 421;
	case UnprocessableEntity = 422;
	case Locked = 423;
	case FailedDependency = 424;
	case TooEarly = 425;
	case UpgradeRequired = 426;
	case PreconditionRequired = 428;
	case TooManyRequests = 429;
	case RequestHeaderFieldsTooLarge = 431;
	case UnavailableForLegalReasons = 451;

	// 5xx
	case InternalServerError = 500;
	case NotImplemented = 501;
	case BadGateway = 502;
	case ServiceUnavailable = 503;
	case GatewayTimeout = 504;
	case HttpVersionNotSupported = 505;
	case InsufficientStorage = 507;
	case LoopDetected = 508;

	// -----------------

	public function label(): string
	{
		return $this->getInfo()['label'];
	}

	public function code(): int
	{
		return $this->value;
	}

	public function message(string $usage = 'web'): string|null
	{
		return $this->getInfo($usage)['message'];
	}

	// -----------------

	protected function getInfo(string|null $usage = null): array
	{
		$target = match ($usage) {
			'api' => 'resource',
			default => 'page'
		};

		return match($this) {
			// 1xx
			StatusCode::Continue => ['label' => 'Continue', 'message' => null],
			StatusCode::SwitchingProtocols => ['label' => 'Switching Protocols', 'message' => null],
			StatusCode::Processing => ['label' => 'Processing', 'message' => null],
			StatusCode::EarlyHints => ['label' => 'Early Hints', 'message' => null],

			// 2xx
			StatusCode::Ok => ['label' => 'Ok', 'message' => null],
			StatusCode::Created => ['label' => 'Created', 'message' => null],
			StatusCode::Accepted => ['label' => 'Accepted', 'message' => null],
			StatusCode::NoContent => ['label' => 'No Content', 'message' => null],
			StatusCode::ResetContent => ['label' => 'Reset Content', 'message' => null],
			StatusCode::PartialContent => ['label' => 'Partial Content', 'message' => null],
			StatusCode::MultiStatus => ['label' => 'Multi-Status', 'message' => null],
			StatusCode::AlreadyReported => ['label' => 'Already Reported', 'message' => null],
			StatusCode::IAmUsed => ['label' => 'I Am Used', 'message' => null],

			// 3xx
			StatusCode::MultipleChoices => ['label' => 'Multiple Choices', 'message' => "The are multiple responses available. Choose one of the given options."],
			StatusCode::MovedPermanently => ['label' => 'Moved Permanently', 'message' => "The location of this $target has been moved permanently."],
			StatusCode::Found => ['label' => 'Found', 'message' => "The location of this $target has been moved temporarily."],
			StatusCode::SeeOther => ['label' => 'See Other', 'message' => "The requested $target can be found at a different URI."],
			StatusCode::NotModified => ['label' => 'Not Modified', 'message' => "The $target has not been modified. Cached version can be used, if present."],
			StatusCode::TemporaryRedirect => ['label' => 'Temporary Redirect', 'message' => "The location of this $target has been moved temporarily."],
			StatusCode::PermanentRedirect => ['label' => 'Permanent Redirect', 'message' => "The location of this $target has been moved permanently."],

			// 4xx
			StatusCode::BadRequest => ['label' => 'Bad Request', 'message' => "A malformed request has been received, and cannot be processed."],
			StatusCode::Unauthorized => ['label' => 'Unauthorized', 'message' => "Authorization is required to access this $target."],
			StatusCode::PaymentRequired => ['label' => 'Payment Required', 'message' => "A payment must be made before this request can be processed."],
			StatusCode::Forbidden => ['label' => 'Forbidden', 'message' => "You do not have permission to access this $target."],
			StatusCode::NotFound => ['label' => 'Not Found', 'message' => "The $target you are looking for does not exist."],
			StatusCode::MethodNotAllowed => ['label' => 'Method Not Allowed', 'message' => "The HTTP method used is not allowed for this $target."],
			StatusCode::NotAcceptable => ['label' => 'Not Acceptable', 'message' => "No $target could be found that matches the given criteria."],
			StatusCode::ProxyAuthenticationRequired => ['label' => 'Proxy Authentication Required', 'message' => "Authorization through a proxy is required to access this $target."],
			StatusCode::RequestTimeout => ['label' => 'Request Timeout', 'message' => "Due to inactivity, the connection has been shut down."],
			StatusCode::Conflict => ['label' => 'Conflict', 'message' => "The request conflicts with the current state of the $target."],
			StatusCode::Gone => ['label' => 'Gone', 'message' => "The $target has been permanently deleted, and no forwarding address is available."],
			StatusCode::LengthRequired => ['label' => 'Length Required', 'message' => "The Content-Length header is required to access this $target."],
			StatusCode::PreconditionFailed => ['label' => 'Precondition Failed', 'message' => "The provided preconditions have not been met."],
			StatusCode::PayloadTooLarge => ['label' => 'Payload Too Large', 'message' => "The provided payload exceeds the configured limits."],
			StatusCode::UriTooLong => ['label' => 'URI Too Long', 'message' => "The URI is too long, and cannot be interpreted."],
			StatusCode::UnsupportedMediaType => ['label' => 'Unsupported Media Type', 'message' => "The payload format is unsupported, and cannot be processed."],
			StatusCode::RangeNotSatisfiable => ['label' => 'Range Not Satisfiable', 'message' => "The range specified in the Range header cannot be fulfilled."],
			StatusCode::ExpectationFailed => ['label' => 'Expectation Failed', 'message' => "The expectation indicated by the Expect request header field cannot be met."],
			StatusCode::Teapot => ['label' => "I'm a teapot", 'message' => "I refuse to brew coffee, because I'm a teapot."],
			StatusCode::MisdirectedRequest => ['label' => 'Misdirected Request', 'message' => "No appropriate response could be returned."],
			StatusCode::UnprocessableEntity => ['label' => 'Unprocessable Entity', 'message' => "The request was well-formed but was unable to be followed due to semantic errors."],
			StatusCode::Locked => ['label' => 'Locked', 'message' => "The resource that is being accessed is locked."],
			StatusCode::FailedDependency => ['label' => 'Failed Dependency', 'message' => "The request failed due to failure of a previous request."],
			StatusCode::TooEarly => ['label' => 'Too Early', 'message' => "Processing this request is too risky, as it might be replayed."],
			StatusCode::UpgradeRequired => ['label' => 'Upgrade Required', 'message' => "An upgrade to a different protocol is required in order to process the request."],
			StatusCode::PreconditionRequired => ['label' => 'Precondition Required', 'message' => "The request must be conditional in order to prevent conflicts."],
			StatusCode::TooManyRequests => ['label' => 'Too Many Requests', 'message' => "The amount of requests to this $target have exceeded the configured limits."],
			StatusCode::RequestHeaderFieldsTooLarge => ['label' => 'Request Header Fields Too Large', 'message' => "One or more provided header field exceeds the configured limits."],
			StatusCode::UnavailableForLegalReasons => ['label' => 'Unavailable For Legal Reasons', 'message' => "This $target cannot be accessed due to pending legal matters."],

			// 5xx
			StatusCode::InternalServerError => ['label' => 'Internal Server Error', 'message' => "There is a problem with this $target, and cannot be displayed."],
			StatusCode::NotImplemented => ['label' => 'Not Implemented', 'message' => "The request is not recognized or cannot be fulfilled."],
			StatusCode::BadGateway => ['label' => 'Bad Gateway', 'message' => "Because of a problem with the upstream server, the $target cannot be retrieved."],
			StatusCode::ServiceUnavailable => ['label' => 'Service Unavailable', 'message' => "The request can't be handled right now. Try again later."],
			StatusCode::GatewayTimeout => ['label' => 'Gateway Timeout', 'message' => "It took too long for the upstream server to return the $target."],
			StatusCode::HttpVersionNotSupported => ['label' => 'HTTP Version Not Supported', 'message' => "The HTTP version used is not supported. Try upgrading to a newer version."],
			StatusCode::InsufficientStorage => ['label' => 'Insufficient Storage', 'message' => "There is not enough storage available to fulfill the request."],
			StatusCode::LoopDetected => ['label' => 'Loop Detected', 'message' => "A loop has been detected while processing the request."],
		};
	}

}