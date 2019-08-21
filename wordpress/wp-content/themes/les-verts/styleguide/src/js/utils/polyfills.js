/* eslint-disable */

/* Polyfill service v3.37.0
 * For detailed credits and licence information see https://github.com/financial-times/polyfill-service.
 *
 * Features requested: Array.prototype.entries,Array.prototype.forEach,CustomEvent,Element.prototype.closest,HTMLPictureElement,Object.assign,Promise,URL,matchMedia
 *
 * - _ESAbstract.ArrayCreate, License: CC0 (required by "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.Call, License: CC0 (required by "Array.prototype.forEach", "_ESAbstract.ToPrimitive", "_ESAbstract.ToString", "_ESAbstract.OrdinaryToPrimitive", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.Get, License: CC0 (required by "Array.prototype.forEach", "Object.assign", "Object.defineProperties", "URL", "_ESAbstract.IsRegExp", "String.prototype.includes", "_ArrayIterator", "Array.prototype.entries", "_ESAbstract.OrdinaryToPrimitive", "_ESAbstract.ToPrimitive", "_ESAbstract.ToString", "Array.prototype.filter", "Symbol", "_Iterator", "Array.prototype.map", "_ESAbstract.ArraySpeciesCreate", "_ESAbstract.GetPrototypeFromConstructor", "_ESAbstract.OrdinaryCreateFromConstructor", "_ESAbstract.Construct")
 * - _ESAbstract.HasProperty, License: CC0 (required by "Array.prototype.forEach", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.IsArray, License: CC0 (required by "Array.isArray", "URL", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.IsCallable, License: CC0 (required by "Array.prototype.forEach", "Function.prototype.bind", "Object.getOwnPropertyDescriptor", "Object.assign", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "_ESAbstract.GetMethod", "_ESAbstract.ToPrimitive", "_ESAbstract.ToString", "_ESAbstract.OrdinaryToPrimitive", "Array.prototype.filter", "Symbol", "Array.prototype.map")
 * - _ESAbstract.RequireObjectCoercible, License: CC0 (required by "String.prototype.includes", "_ArrayIterator", "Array.prototype.entries")
 * - _ESAbstract.ToBoolean, License: CC0 (required by "_ESAbstract.IsRegExp", "String.prototype.includes", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.filter", "Symbol", "_Iterator")
 * - _ESAbstract.ToInteger, License: CC0 (required by "_ESAbstract.ToLength", "Array.prototype.forEach", "String.prototype.includes", "_ArrayIterator", "Array.prototype.entries")
 * - _ESAbstract.ToLength, License: CC0 (required by "Array.prototype.forEach", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.ToObject, License: CC0 (required by "Array.prototype.entries", "Array.prototype.forEach", "Object.assign", "Object.defineProperties", "URL", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.map", "_ESAbstract.GetV", "_ESAbstract.GetMethod", "_ESAbstract.ToPrimitive", "_ESAbstract.ToString")
 * - _ESAbstract.GetV, License: CC0 (required by "_ESAbstract.GetMethod", "_ESAbstract.ToPrimitive", "_ESAbstract.ToString", "Array.prototype.forEach")
 * - _ESAbstract.GetMethod, License: CC0 (required by "_ESAbstract.ToPrimitive", "_ESAbstract.ToString", "Array.prototype.forEach", "_ESAbstract.IsConstructor", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.Type, License: CC0 (required by "_ESAbstract.ToString", "Array.prototype.forEach", "Object.defineProperties", "URL", "Object.create", "_ArrayIterator", "Array.prototype.entries", "_ESAbstract.ToPrimitive", "_ESAbstract.IsRegExp", "String.prototype.includes", "_ESAbstract.OrdinaryToPrimitive", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "Array.prototype.map", "_ESAbstract.IsConstructor", "_ESAbstract.GetPrototypeFromConstructor", "_ESAbstract.OrdinaryCreateFromConstructor", "_ESAbstract.Construct")
 * - _ESAbstract.GetPrototypeFromConstructor, License: CC0 (required by "_ESAbstract.OrdinaryCreateFromConstructor", "_ESAbstract.Construct", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.IsConstructor, License: CC0 (required by "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map", "_ESAbstract.Construct")
 * - _ESAbstract.IsRegExp, License: CC0 (required by "String.prototype.includes", "_ArrayIterator", "Array.prototype.entries")
 * - _ESAbstract.OrdinaryToPrimitive, License: CC0 (required by "_ESAbstract.ToPrimitive", "_ESAbstract.ToString", "Array.prototype.forEach")
 * - _ESAbstract.ToPrimitive, License: CC0 (required by "_ESAbstract.ToString", "Array.prototype.forEach")
 * - _ESAbstract.ToString, License: CC0 (required by "Array.prototype.forEach", "String.prototype.includes", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.filter", "Symbol", "_Iterator", "Array.prototype.map")
 * - document, License: CC0 (required by "Event", "CustomEvent", "matchMedia", "~html5-elements", "HTMLPictureElement", "Element", "Element.prototype.matches", "Element.prototype.closest", "document.querySelector")
 * - ~html5-elements, License: MIT (required by "HTMLPictureElement")
 * - Element, License: CC0 (required by "Event", "CustomEvent", "matchMedia", "Element.prototype.matches", "Element.prototype.closest", "document.querySelector")
 * - document.querySelector, License: CC0 (required by "Element.prototype.matches", "Element.prototype.closest")
 * - Element.prototype.matches, License: CC0 (required by "Element.prototype.closest")
 * - Element.prototype.closest, License: CC0
 * - HTMLPictureElement, License: MIT
 * - Object.defineProperty, License: CC0 (required by "_ArrayIterator", "Array.prototype.entries", "_ESAbstract.CreateMethodProperty", "Array.prototype.forEach", "Object.assign", "Event", "CustomEvent", "matchMedia", "Object.defineProperties", "URL", "_Iterator", "Object.setPrototypeOf", "Symbol", "Symbol.iterator", "Symbol.toStringTag", "_ESAbstract.CreateDataProperty", "_ESAbstract.CreateDataPropertyOrThrow", "Array.prototype.filter", "Array.prototype.map", "_ESAbstract.OrdinaryCreateFromConstructor", "_ESAbstract.Construct", "_ESAbstract.ArraySpeciesCreate")
 * - _ESAbstract.CreateDataProperty, License: CC0 (required by "_ESAbstract.CreateDataPropertyOrThrow", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.CreateDataPropertyOrThrow, License: CC0 (required by "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.CreateMethodProperty, License: CC0 (required by "Array.prototype.entries", "Array.prototype.forEach", "Object.assign", "Object.getOwnPropertyDescriptor", "Object.keys", "Object.defineProperties", "URL", "Array.isArray", "Object.create", "_ArrayIterator", "Object.setPrototypeOf", "String.prototype.includes", "Function.prototype.bind", "_Iterator", "Object.getPrototypeOf", "Object.getOwnPropertyNames", "Symbol", "Array.prototype.filter", "Array.prototype.map", "Object.freeze")
 * - Array.isArray, License: CC0 (required by "URL")
 * - Array.prototype.forEach, License: CC0 (required by "URL", "Object.setPrototypeOf", "_ArrayIterator", "Array.prototype.entries", "Symbol", "_Iterator")
 * - Function.prototype.bind, License: MIT (required by "Object.getOwnPropertyDescriptor", "Object.assign", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "_ESAbstract.Construct", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "Array.prototype.map")
 * - Object.freeze, License: CC0 (required by "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - Object.getOwnPropertyDescriptor, License: CC0 (required by "Object.assign", "Object.defineProperties", "URL", "Object.setPrototypeOf", "_ArrayIterator", "Array.prototype.entries", "Symbol", "_Iterator")
 * - Object.getOwnPropertyNames, License: CC0 (required by "Object.setPrototypeOf", "_ArrayIterator", "Array.prototype.entries", "Symbol", "_Iterator")
 * - Object.getPrototypeOf, License: CC0 (required by "Object.setPrototypeOf", "_ArrayIterator", "Array.prototype.entries", "_ESAbstract.OrdinaryCreateFromConstructor", "_ESAbstract.Construct", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "Array.prototype.map")
 * - Object.keys, License: MIT (required by "Object.assign", "Object.defineProperties", "URL", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - Object.assign, License: CC0 (required by "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - Object.defineProperties, License: CC0 (required by "URL", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Object.create")
 * - Object.create, License: CC0 (required by "_ArrayIterator", "Array.prototype.entries", "Object.setPrototypeOf", "Symbol", "_Iterator", "_ESAbstract.OrdinaryCreateFromConstructor", "_ESAbstract.Construct", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Array.prototype.map")
 * - _ESAbstract.OrdinaryCreateFromConstructor, License: CC0 (required by "_ESAbstract.Construct", "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.Construct, License: CC0 (required by "_ESAbstract.ArraySpeciesCreate", "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - _ESAbstract.ArraySpeciesCreate, License: CC0 (required by "Array.prototype.filter", "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Array.prototype.map")
 * - Array.prototype.filter, License: CC0 (required by "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - Array.prototype.map, License: CC0 (required by "Symbol", "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - Object.setPrototypeOf, License: MIT (required by "_ArrayIterator", "Array.prototype.entries")
 * - Promise, License: MIT
 * - String.prototype.includes, License: CC0 (required by "_ArrayIterator", "Array.prototype.entries")
 * - Symbol, License: MIT (required by "_Iterator", "_ArrayIterator", "Array.prototype.entries", "Symbol.iterator", "Symbol.toStringTag")
 * - Symbol.iterator, License: MIT (required by "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - Symbol.toStringTag, License: MIT (required by "_Iterator", "_ArrayIterator", "Array.prototype.entries")
 * - _Iterator, License: MIT (required by "_ArrayIterator", "Array.prototype.entries")
 * - _ArrayIterator, License: MIT (required by "Array.prototype.entries")
 * - Array.prototype.entries, License: CC0
 * - URL, License: CC0-1.0
 * - Window, License: CC0 (required by "Event", "CustomEvent", "matchMedia")
 * - Event, License: CC0 (required by "CustomEvent", "matchMedia")
 * - CustomEvent, License: CC0
 * - matchMedia, License: CC0 */

( function( undefined ) {

// _ESAbstract.ArrayCreate
// 9.4.2.2. ArrayCreate ( length [ , proto ] )
	function ArrayCreate( length /* [, proto] */ ) { // eslint-disable-line no-unused-vars
		// 1. Assert: length is an integer Number ≥ 0.
		// 2. If length is -0, set length to +0.
		if (1 / length === - Infinity) {
			length = 0;
		}
		// 3. If length>2^32-1, throw a RangeError exception.
		if (length > ( Math.pow( 2, 32 ) - 1 )) {
			throw new RangeError( 'Invalid array length' );
		}
		// 4. If proto is not present, set proto to the intrinsic object
		// %ArrayPrototype%. 5. Let A be a newly created Array exotic object.
		var A = [];
		// 6. Set A's essential internal methods except for
		// [[DefineOwnProperty]] to the default ordinary object definitions
		// specified in 9.1. 7. Set A.[[DefineOwnProperty]] as specified in
		// 9.4.2.1. 8. Set A.[[Prototype]] to proto. 9. Set A.[[Extensible]] to
		// true. 10. Perform ! OrdinaryDefineOwnProperty(A, "length",
		// PropertyDescriptor{[[Value]]: length, [[Writable]]: true,
		// [[Enumerable]]: false, [[Configurable]]: false}).
		A.length = length;
		// 11. Return A.
		return A;
	}

// _ESAbstract.Call
	/* global IsCallable */

// 7.3.12. Call ( F, V [ , argumentsList ] )
	function Call( F, V /* [, argumentsList] */ ) { // eslint-disable-line no-unused-vars
		// 1. If argumentsList is not present, set argumentsList to a new empty
		// List.
		var argumentsList = arguments.length > 2 ? arguments[ 2 ] : [];
		// 2. If IsCallable(F) is false, throw a TypeError exception.
		if (IsCallable( F ) === false) {
			throw new TypeError(
				Object.prototype.toString.call( F ) + 'is not a function.' );
		}
		// 3. Return ? F.[[Call]](V, argumentsList).
		return F.apply( V, argumentsList );
	}

// _ESAbstract.Get
// 7.3.1. Get ( O, P )
	function Get( O, P ) { // eslint-disable-line no-unused-vars
		// 1. Assert: Type(O) is Object.
		// 2. Assert: IsPropertyKey(P) is true.
		// 3. Return ? O.[[Get]](P, O).
		return O[ P ];
	}

// _ESAbstract.HasProperty
// 7.3.10. HasProperty ( O, P )
	function HasProperty( O, P ) { // eslint-disable-line no-unused-vars
		// Assert: Type(O) is Object.
		// Assert: IsPropertyKey(P) is true.
		// Return ? O.[[HasProperty]](P).
		return P in O;
	}

// _ESAbstract.IsArray
// 7.2.2. IsArray ( argument )
	function IsArray( argument ) { // eslint-disable-line no-unused-vars
		// 1. If Type(argument) is not Object, return false.
		// 2. If argument is an Array exotic object, return true.
		// 3. If argument is a Proxy exotic object, then
		// a. If argument.[[ProxyHandler]] is null, throw a TypeError exception.
		// b. Let target be argument.[[ProxyTarget]].
		// c. Return ? IsArray(target).
		// 4. Return false.

		// Polyfill.io - We can skip all the above steps and check the string
		// returned from Object.prototype.toString().
		return Object.prototype.toString.call( argument ) === '[object Array]';
	}

// _ESAbstract.IsCallable
// 7.2.3. IsCallable ( argument )
	function IsCallable( argument ) { // eslint-disable-line no-unused-vars
		// 1. If Type(argument) is not Object, return false.
		// 2. If argument has a [[Call]] internal method, return true.
		// 3. Return false.

		// Polyfill.io - Only function objects have a [[Call]] internal method.
		// This means we can simplify this function to check that the argument
		// has a type of function.
		return typeof argument === 'function';
	}

// _ESAbstract.RequireObjectCoercible
// 7.2.1. RequireObjectCoercible ( argument )
// The abstract operation ToObject converts argument to a value of type Object
// according to Table 12: Table 12: ToObject Conversions
	/*
  |----------------------------------------------------------------------------------------------------------------------------------------------------|
  | Argument Type | Result                                                                                                                             |
  |----------------------------------------------------------------------------------------------------------------------------------------------------|
  | Undefined     | Throw a TypeError exception.                                                                                                       |
  | Null          | Throw a TypeError exception.                                                                                                       |
  | Boolean       | Return argument.                                                                                                                   |
  | Number        | Return argument.                                                                                                                   |
  | String        | Return argument.                                                                                                                   |
  | Symbol        | Return argument.                                                                                                                   |
  | Object        | Return argument.                                                                                                                   |
  |----------------------------------------------------------------------------------------------------------------------------------------------------|
  */
	function RequireObjectCoercible( argument ) { // eslint-disable-line no-unused-vars
		if (argument === null || argument === undefined) {
			throw TypeError();
		}
		return argument;
	}

// _ESAbstract.ToBoolean
// 7.1.2. ToBoolean ( argument )
// The abstract operation ToBoolean converts argument to a value of type
// Boolean according to Table 9:
	/*
  --------------------------------------------------------------------------------------------------------------
  | Argument Type | Result                                                                                     |
  --------------------------------------------------------------------------------------------------------------
  | Undefined     | Return false.                                                                              |
  | Null          | Return false.                                                                              |
  | Boolean       | Return argument.                                                                           |
  | Number        | If argument is +0, -0, or NaN, return false; otherwise return true.                        |
  | String        | If argument is the empty String (its length is zero), return false; otherwise return true. |
  | Symbol        | Return true.                                                                               |
  | Object        | Return true.                                                                               |
  --------------------------------------------------------------------------------------------------------------
  */
	function ToBoolean( argument ) { // eslint-disable-line no-unused-vars
		return Boolean( argument );
	}

// _ESAbstract.ToInteger
// 7.1.4. ToInteger ( argument )
	function ToInteger( argument ) { // eslint-disable-line no-unused-vars
		// 1. Let number be ? ToNumber(argument).
		var number = Number( argument );
		// 2. If number is NaN, return +0.
		if (isNaN( number )) {
			return 0;
		}
		// 3. If number is +0, -0, +∞, or -∞, return number.
		if (1 / number === Infinity || 1 / number === - Infinity || number ===
			Infinity || number === - Infinity) {
			return number;
		}
		// 4. Return the number value that is the same sign as number and whose
		// magnitude is floor(abs(number)).
		return ( ( number < 0 ) ? - 1 : 1 ) * Math.floor( Math.abs( number ) );
	}

// _ESAbstract.ToLength
	/* global ToInteger */

// 7.1.15. ToLength ( argument )
	function ToLength( argument ) { // eslint-disable-line no-unused-vars
		// 1. Let len be ? ToInteger(argument).
		var len = ToInteger( argument );
		// 2. If len ≤ +0, return +0.
		if (len <= 0) {
			return 0;
		}
		// 3. Return min(len, 253-1).
		return Math.min( len, Math.pow( 2, 53 ) - 1 );
	}

// _ESAbstract.ToObject
// 7.1.13 ToObject ( argument )
// The abstract operation ToObject converts argument to a value of type Object
// according to Table 12: Table 12: ToObject Conversions
	/*
  |----------------------------------------------------------------------------------------------------------------------------------------------------|
  | Argument Type | Result                                                                                                                             |
  |----------------------------------------------------------------------------------------------------------------------------------------------------|
  | Undefined     | Throw a TypeError exception.                                                                                                       |
  | Null          | Throw a TypeError exception.                                                                                                       |
  | Boolean       | Return a new Boolean object whose [[BooleanData]] internal slot is set to argument. See 19.3 for a description of Boolean objects. |
  | Number        | Return a new Number object whose [[NumberData]] internal slot is set to argument. See 20.1 for a description of Number objects.    |
  | String        | Return a new String object whose [[StringData]] internal slot is set to argument. See 21.1 for a description of String objects.    |
  | Symbol        | Return a new Symbol object whose [[SymbolData]] internal slot is set to argument. See 19.4 for a description of Symbol objects.    |
  | Object        | Return argument.                                                                                                                   |
  |----------------------------------------------------------------------------------------------------------------------------------------------------|
  */
	function ToObject( argument ) { // eslint-disable-line no-unused-vars
		if (argument === null || argument === undefined) {
			throw TypeError();
		}
		return Object( argument );
	}

// _ESAbstract.GetV
	/* global ToObject */

// 7.3.2 GetV (V, P)
	function GetV( v, p ) { // eslint-disable-line no-unused-vars
		// 1. Assert: IsPropertyKey(P) is true.
		// 2. Let O be ? ToObject(V).
		var o = ToObject( v );
		// 3. Return ? O.[[Get]](P, V).
		return o[ p ];
	}

// _ESAbstract.GetMethod
	/* global GetV, IsCallable */

// 7.3.9. GetMethod ( V, P )
	function GetMethod( V, P ) { // eslint-disable-line no-unused-vars
		// 1. Assert: IsPropertyKey(P) is true.
		// 2. Let func be ? GetV(V, P).
		var func = GetV( V, P );
		// 3. If func is either undefined or null, return undefined.
		if (func === null || func === undefined) {
			return undefined;
		}
		// 4. If IsCallable(func) is false, throw a TypeError exception.
		if (IsCallable( func ) === false) {
			throw new TypeError( 'Method not callable: ' + P );
		}
		// 5. Return func.
		return func;
	}

// _ESAbstract.Type
// "Type(x)" is used as shorthand for "the type of x"...
	function Type( x ) { // eslint-disable-line no-unused-vars
		switch ( typeof x ) {
			case 'undefined':
				return 'undefined';
			case 'boolean':
				return 'boolean';
			case 'number':
				return 'number';
			case 'string':
				return 'string';
			case 'symbol':
				return 'symbol';
			default:
				// typeof null is 'object'
				if (x === null) {
					return 'null';
				}
				// Polyfill.io - This is here because a Symbol polyfill will
				// have a typeof `object`.
				if ('Symbol' in this && x instanceof this.Symbol) {
					return 'symbol';
				}
				return 'object';
		}
	}

// _ESAbstract.GetPrototypeFromConstructor
	/* global Get, Type */

// 9.1.14. GetPrototypeFromConstructor ( constructor, intrinsicDefaultProto )
	function GetPrototypeFromConstructor( constructor, intrinsicDefaultProto ) { // eslint-disable-line no-unused-vars
		// 1. Assert: intrinsicDefaultProto is a String value that is this
		// specification's name of an intrinsic object. The corresponding
		// object must be an intrinsic that is intended to be used as the
		// [[Prototype]] value of an object. 2. Assert: IsCallable(constructor)
		// is true. 3. Let proto be ? Get(constructor, "prototype").
		var proto = Get( constructor, 'prototype' );
		// 4. If Type(proto) is not Object, then
		if (Type( proto ) !== 'object') {
			// a. Let realm be ? GetFunctionRealm(constructor).
			// b. Set proto to realm's intrinsic object named
			// intrinsicDefaultProto.
			proto = intrinsicDefaultProto;
		}
		// 5. Return proto.
		return proto;
	}

// _ESAbstract.IsConstructor
	/* global Type */

// 7.2.4. IsConstructor ( argument )
	function IsConstructor( argument ) { // eslint-disable-line no-unused-vars
		// 1. If Type(argument) is not Object, return false.
		if (Type( argument ) !== 'object') {
			return false;
		}
		// 2. If argument has a [[Construct]] internal method, return true.
		// 3. Return false.

		// Polyfill.io - `new argument` is the only way  to truly test if a
		// function is a constructor. We choose to not use`new argument`
		// because the argument could have side effects when called. Instead we
		// check to see if the argument is a function and if it has a
		// prototype. Arrow functions do not have a [[Construct]] internal
		// method, nor do they have a prototype.
		return typeof argument === 'function' && !!argument.prototype;
	}

// _ESAbstract.IsRegExp
	/* global Type, Get, ToBoolean */

// 7.2.8. IsRegExp ( argument )
	function IsRegExp( argument ) { // eslint-disable-line no-unused-vars
		// 1. If Type(argument) is not Object, return false.
		if (Type( argument ) !== 'object') {
			return false;
		}
		// 2. Let matcher be ? Get(argument, @@match).
		var matcher = 'Symbol' in this && 'match' in this.Symbol ? Get( argument,
			this.Symbol.match ) : undefined;
		// 3. If matcher is not undefined, return ToBoolean(matcher).
		if (matcher !== undefined) {
			return ToBoolean( matcher );
		}
		// 4. If argument has a [[RegExpMatcher]] internal slot, return true.
		try {
			var lastIndex = argument.lastIndex;
			argument.lastIndex = 0;
			RegExp.prototype.exec.call( argument );
			return true;
		}
		catch ( e ) {}
		finally {
			argument.lastIndex = lastIndex;
		}
		// 5. Return false.
		return false;
	}

// _ESAbstract.OrdinaryToPrimitive
	/* global Get, IsCallable, Call, Type */

// 7.1.1.1. OrdinaryToPrimitive ( O, hint )
	function OrdinaryToPrimitive( O, hint ) { // eslint-disable-line no-unused-vars
		// 1. Assert: Type(O) is Object.
		// 2. Assert: Type(hint) is String and its value is either "string" or
		// "number". 3. If hint is "string", then
		if (hint === 'string') {
			// a. Let methodNames be « "toString", "valueOf" ».
			var methodNames = [ 'toString', 'valueOf' ];
			// 4. Else,
		}
		else {
			// a. Let methodNames be « "valueOf", "toString" ».
			methodNames = [ 'valueOf', 'toString' ];
		}
		// 5. For each name in methodNames in List order, do
		for (var i = 0; i < methodNames.length; ++ i) {
			var name = methodNames[ i ];
			// a. Let method be ? Get(O, name).
			var method = Get( O, name );
			// b. If IsCallable(method) is true, then
			if (IsCallable( method )) {
				// i. Let result be ? Call(method, O).
				var result = Call( method, O );
				// ii. If Type(result) is not Object, return result.
				if (Type( result ) !== 'object') {
					return result;
				}
			}
		}
		// 6. Throw a TypeError exception.
		throw new TypeError( 'Cannot convert to primitive.' );
	}

// _ESAbstract.ToPrimitive
	/* global Type, GetMethod, Call, OrdinaryToPrimitive */

// 7.1.1. ToPrimitive ( input [ , PreferredType ] )
	function ToPrimitive( input /* [, PreferredType] */ ) { // eslint-disable-line no-unused-vars
		var PreferredType = arguments.length > 1 ? arguments[ 1 ] : undefined;
		// 1. Assert: input is an ECMAScript language value.
		// 2. If Type(input) is Object, then
		if (Type( input ) === 'object') {
			// a. If PreferredType is not present, let hint be "default".
			if (arguments.length < 2) {
				var hint = 'default';
				// b. Else if PreferredType is hint String, let hint be
				// "string".
			}
			else if (PreferredType === String) {
				hint = 'string';
				// c. Else PreferredType is hint Number, let hint be "number".
			}
			else if (PreferredType === Number) {
				hint = 'number';
			}
			// d. Let exoticToPrim be ? GetMethod(input, @@toPrimitive).
			var exoticToPrim = typeof this.Symbol === 'function' &&
			typeof this.Symbol.toPrimitive === 'symbol' ? GetMethod( input,
				this.Symbol.toPrimitive ) : undefined;
			// e. If exoticToPrim is not undefined, then
			if (exoticToPrim !== undefined) {
				// i. Let result be ? Call(exoticToPrim, input, « hint »).
				var result = Call( exoticToPrim, input, [ hint ] );
				// ii. If Type(result) is not Object, return result.
				if (Type( result ) !== 'object') {
					return result;
				}
				// iii. Throw a TypeError exception.
				throw new TypeError( 'Cannot convert exotic object to primitive.' );
			}
			// f. If hint is "default", set hint to "number".
			if (hint === 'default') {
				hint = 'number';
			}
			// g. Return ? OrdinaryToPrimitive(input, hint).
			return OrdinaryToPrimitive( input, hint );
		}
		// 3. Return input
		return input;
	}

// _ESAbstract.ToString
	/* global Type, ToPrimitive */
// 7.1.12. ToString ( argument )
// The abstract operation ToString converts argument to a value of type String
// according to Table 11: Table 11: ToString Conversions
	/*
  |---------------|--------------------------------------------------------|
  | Argument Type | Result                                                 |
  |---------------|--------------------------------------------------------|
  | Undefined     | Return "undefined".                                    |
  |---------------|--------------------------------------------------------|
  | Null	        | Return "null".                                         |
  |---------------|--------------------------------------------------------|
  | Boolean       | If argument is true, return "true".                    |
  |               | If argument is false, return "false".                  |
  |---------------|--------------------------------------------------------|
  | Number        | Return NumberToString(argument).                       |
  |---------------|--------------------------------------------------------|
  | String        | Return argument.                                       |
  |---------------|--------------------------------------------------------|
  | Symbol        | Throw a TypeError exception.                           |
  |---------------|--------------------------------------------------------|
  | Object        | Apply the following steps:                             |
  |               | Let primValue be ? ToPrimitive(argument, hint String). |
  |               | Return ? ToString(primValue).                          |
  |---------------|--------------------------------------------------------|
  */
	function ToString( argument ) { // eslint-disable-line no-unused-vars
		switch ( Type( argument ) ) {
			case 'symbol':
				throw new TypeError( 'Cannot convert a Symbol value to a string' );
				break;
			case 'object':
				var primValue = ToPrimitive( argument, 'string' );
				return ToString( primValue );
			default:
				return String( argument );
		}
	}

	if (!( 'document' in this
	)) {

// document
		if (( typeof WorkerGlobalScope === 'undefined' ) &&
			( typeof importScripts !== 'function' )) {

			if (this.HTMLDocument) { // IE8

				// HTMLDocument is an extension of Document.  If the browser
				// has HTMLDocument but not Document, the former will suffice
				// as an alias for the latter.
				this.Document = this.HTMLDocument;

			}
			else {

				// Create an empty function to act as the missing constructor
				// for the document object, attach the document object as its
				// prototype.  The function needs to be anonymous else it is
				// hoisted and causes the feature detect to prematurely pass,
				// preventing the assignments below being made.
				this.Document = this.HTMLDocument = document.constructor = ( new Function(
					'return function Document() {}' )() );
				this.Document.prototype = document;
			}
		}

	}

	if (!( ( function() {
			var e = document.createElement( 'p' ), t = !1;
			return e.innerHTML = '<section></section>', document.documentElement.appendChild(
				e ), e.firstChild && ( 'getComputedStyle' in window ? t = 'block' ===
				getComputedStyle( e.firstChild ).display : e.firstChild.currentStyle &&
				( t = 'block' ===
					e.firstChild.currentStyle.display ) ), document.documentElement.removeChild(
				e ), t;
		} )()
	)) {

// ~html5-elements
		/**
		 * @preserve HTML5 Shiv 3.7.3 | @afarkas @jdalton @jon_neal @rem |
		 *     MIT/GPL2 Licensed
		 */
		!function( a, b ) {
			function c( a, b ) {
				var c = a.createElement( 'p' ),
					d = a.getElementsByTagName( 'head' )[ 0 ] || a.documentElement;
				return c.innerHTML = 'x<style>' + b + '</style>', d.insertBefore(
					c.lastChild, d.firstChild );
			}

			function d() {
				var a = t.elements;
				return 'string' == typeof a ? a.split( ' ' ) : a;
			}

			function e( a, b ) {
				var c = t.elements;
				'string' != typeof c && ( c = c.join( ' ' ) ), 'string' != typeof a &&
				( a = a.join( ' ' ) ), t.elements = c + ' ' + a, j( b );
			}

			function f( a ) {
				var b = s[ a[ q ] ];
				return b || ( b = {}, r ++, a[ q ] = r, s[ r ] = b ), b;
			}

			function g( a, c, d ) {
				if (c || ( c = b ), l) {
					return c.createElement( a );
				}
				d || ( d = f( c ) );
				var e;
				return e = d.cache[ a ] ? d.cache[ a ].cloneNode() : p.test( a )
					? ( d.cache[ a ] = d.createElem( a ) ).cloneNode()
					: d.createElem( a ), !e.canHaveChildren || o.test( a ) || e.tagUrn
					? e
					: d.frag.appendChild( e );
			}

			function h( a, c ) {
				if (a || ( a = b ), l) {
					return a.createDocumentFragment();
				}
				c = c || f( a );
				for (var e = c.frag.cloneNode(), g = 0, h = d(), i = h.length; i >
				g; g ++) {
					e.createElement( h[ g ] );
				}
				return e;
			}

			function i( a, b ) {
				b.cache ||
				( b.cache = {}, b.createElem = a.createElement, b.createFrag = a.createDocumentFragment, b.frag = b.createFrag() ), a.createElement = function( c ) {
					return t.shivMethods
						? g( c, a, b )
						: b.createElem( c );
				}, a.createDocumentFragment = Function( 'h,f',
					'return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&(' +
					d().
						join().
						replace( /[\w\-:]+/g, function( a ) {
							return b.createElem( a ), b.frag.createElement( a ), 'c("' + a +
							'")';
						} ) + ');return n}' )( t, b.frag );
			}

			function j( a ) {
				a || ( a = b );
				var d = f( a );
				return !t.shivCSS || k || d.hasCSS || ( d.hasCSS = !!c( a,
					'article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}' ) ), l ||
				i( a, d ), a;
			}

			var k, l, m = '3.7.3-pre', n = a.html5 || {},
				o = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,
				p = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,
				q = '_html5shiv', r = 0, s = {};
			!function() {
				try {
					var a = b.createElement( 'a' );
					a.innerHTML = '<xyz></xyz>', k = 'hidden' in a, l = 1 ==
						a.childNodes.length || function() {
							b.createElement( 'a' );
							var a = b.createDocumentFragment();
							return 'undefined' == typeof a.cloneNode || 'undefined' ==
								typeof a.createDocumentFragment || 'undefined' ==
								typeof a.createElement;
						}();
				}
				catch ( c ) {k = !0, l = !0;}
			}();
			var t = {
				elements: n.elements ||
					'abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output picture progress section summary template time video',
				version: m,
				shivCSS: n.shivCSS !== !1,
				supportsUnknownElements: l,
				shivMethods: n.shivMethods !== !1,
				type: 'default',
				shivDocument: j,
				createElement: g,
				createDocumentFragment: h,
				addElements: e,
			};
			a.html5 = t, j( b ), 'object' == typeof module && module.exports &&
			( module.exports = t );
		}( 'undefined' != typeof window ? window : this, document );
	}

	if (!( 'Element' in this && 'HTMLElement' in this
	)) {

// Element
		( function() {

			// IE8
			if (window.Element && !window.HTMLElement) {
				window.HTMLElement = window.Element;
				return;
			}

			// create Element constructor
			window.Element = window.HTMLElement = new Function(
				'return function Element() {}' )();

			// generate sandboxed iframe
			var vbody = document.appendChild( document.createElement( 'body' ) );
			var frame = vbody.appendChild( document.createElement( 'iframe' ) );

			// use sandboxed iframe to replicate Element functionality
			var frameDocument = frame.contentWindow.document;
			var prototype = Element.prototype = frameDocument.appendChild(
				frameDocument.createElement( '*' ) );
			var cache = {};

			// polyfill Element.prototype on an element
			var shiv = function( element, deep ) {
				var
					childNodes = element.childNodes || [],
					index = - 1,
					key, value, childNode;

				if (element.nodeType === 1 && element.constructor !== Element) {
					element.constructor = Element;

					for (key in cache) {
						value = cache[ key ];
						element[ key ] = value;
					}
				}

				while (childNode = deep && childNodes[ ++ index ]) {
					shiv( childNode, deep );
				}

				return element;
			};

			var elements = document.getElementsByTagName( '*' );
			var nativeCreateElement = document.createElement;
			var interval;
			var loopLimit = 100;

			prototype.attachEvent( 'onpropertychange', function( event ) {
				var
					propertyName = event.propertyName,
					nonValue = !cache.hasOwnProperty( propertyName ),
					newValue = prototype[ propertyName ],
					oldValue = cache[ propertyName ],
					index = - 1,
					element;

				while (element = elements[ ++ index ]) {
					if (element.nodeType === 1) {
						if (nonValue || element[ propertyName ] === oldValue) {
							element[ propertyName ] = newValue;
						}
					}
				}

				cache[ propertyName ] = newValue;
			} );

			prototype.constructor = Element;

			if (!prototype.hasAttribute) {
				// <Element>.hasAttribute
				prototype.hasAttribute = function hasAttribute( name ) {
					return this.getAttribute( name ) !== null;
				};
			}

			// Apply Element prototype to the pre-existing DOM as soon as the
			// body element appears.
			function bodyCheck() {
				if (!( loopLimit -- )) {
					clearTimeout( interval );
				}
				if (document.body && !document.body.prototype &&
					/(complete|interactive)/.test( document.readyState )) {
					shiv( document, true );
					if (interval && document.body.prototype) {
						clearTimeout( interval );
					}
					return ( !!document.body.prototype );
				}
				return false;
			}

			if (!bodyCheck()) {
				document.onreadystatechange = bodyCheck;
				interval = setInterval( bodyCheck, 25 );
			}

			// Apply to any new elements created after load
			document.createElement = function createElement( nodeName ) {
				var element = nativeCreateElement( String( nodeName ).toLowerCase() );
				return shiv( element );
			};

			// remove sandboxed iframe
			document.removeChild( vbody );
		}() );

	}

	if (!( 'document' in this && 'querySelector' in this.document
	)) {

// document.querySelector
		( function() {
			var
				head = document.getElementsByTagName( 'head' )[ 0 ];

			function getElementsByQuery( node, selector, one ) {
				var
					generator = document.createElement( 'div' ),
					id = 'qsa' + String( Math.random() ).slice( 3 ),
					style, elements;

				generator.innerHTML = 'x<style>' + selector + '{qsa:' + id + ';}';

				style = head.appendChild( generator.lastChild );

				elements = getElements( node, selector, one, id );

				head.removeChild( style );

				return one ? elements[ 0 ] : elements;
			}

			function getElements( node, selector, one, id ) {
				var
					validNode = /1|9/.test( node.nodeType ),
					childNodes = node.childNodes,
					elements = [],
					index = - 1,
					childNode;

				if (validNode && node.currentStyle && node.currentStyle.qsa === id) {
					if (elements.push( node ) && one) {
						return elements;
					}
				}

				while (childNode = childNodes[ ++ index ]) {
					elements = elements.concat(
						getElements( childNode, selector, one, id ) );

					if (one && elements.length) {
						return elements;
					}
				}

				return elements;
			}

			Document.prototype.querySelector = Element.prototype.querySelector = function querySelectorAll( selector ) {
				return getElementsByQuery( this, selector, true );
			};

			Document.prototype.querySelectorAll = Element.prototype.querySelectorAll = function querySelectorAll( selector ) {
				return getElementsByQuery( this, selector, false );
			};
		}() );

	}

	if (!( 'document' in this && 'matches' in document.documentElement
	)) {

// Element.prototype.matches
		Element.prototype.matches = Element.prototype.webkitMatchesSelector ||
			Element.prototype.oMatchesSelector ||
			Element.prototype.msMatchesSelector ||
			Element.prototype.mozMatchesSelector || function matches( selector ) {

				var element = this;
				var elements = ( element.document ||
					element.ownerDocument ).querySelectorAll( selector );
				var index = 0;

				while (elements[ index ] && elements[ index ] !== element) {
					++ index;
				}

				return !!elements[ index ];
			};

	}

	if (!( 'document' in this && 'closest' in document.documentElement
	)) {

// Element.prototype.closest
		Element.prototype.closest = function closest( selector ) {
			var node = this;

			while (node) {
				if (node.matches( selector )) {
					return node;
				}
				else {
					node = 'SVGElement' in window && node instanceof SVGElement
						? node.parentNode
						: node.parentElement;
				}
			}

			return null;
		};

	}

	if (!( 'HTMLPictureElement' in this || 'picturefill' in this
	)) {

// HTMLPictureElement
		/*! picturefill - v3.0.2 - 2016-02-12
	 * https://scottjehl.github.io/picturefill/
	 * Copyright (c) 2016 https://github.com/scottjehl/picturefill/blob/master/Authors.txt; Licensed MIT
	 */
		!function( a ) {
			var b = navigator.userAgent;
			a.HTMLPictureElement && /ecko/.test( b ) && b.match( /rv\:(\d+)/ ) &&
			RegExp.$1 < 45 && addEventListener( 'resize', function() {
				var b, c = document.createElement( 'source' ), d = function( a ) {
						var b, d, e = a.parentNode;
						'PICTURE' === e.nodeName.toUpperCase()
							? ( b = c.cloneNode(), e.insertBefore( b,
							e.firstElementChild ), setTimeout(
							function() {e.removeChild( b );} ) )
							: ( !a._pfLastSize || a.offsetWidth > a._pfLastSize ) &&
							( a._pfLastSize = a.offsetWidth, d = a.sizes, a.sizes += ',100vw', setTimeout(
								function() {a.sizes = d;} ) );
					}, e = function() {
						var a, b = document.querySelectorAll(
							'picture > img, img[srcset][sizes]' );
						for (a = 0; a < b.length; a ++) {
							d( b[ a ] );
						}
					}, f = function() {clearTimeout( b ), b = setTimeout( e, 99 );},
					g = a.matchMedia && matchMedia( '(orientation: landscape)' ),
					h = function() {f(), g && g.addListener && g.addListener( f );};
				return c.srcset = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==', /^[c|i]|d$/.test(
					document.readyState || '' ) ? h() : document.addEventListener(
					'DOMContentLoaded', h ), f;
			}() );
		}( window ), function( a, b, c ) {
			'use strict';

			function d( a ) {
				return ' ' === a || '	' === a || '\n' === a || '\f' === a || '\r' === a;
			}

			function e( b, c ) {
				var d = new a.Image;
				return d.onerror = function() {A[ b ] = !1, ba();}, d.onload = function() {
					A[ b ] = 1 === d.width, ba();
				}, d.src = c, 'pending';
			}

			function f() {
				M = !1, P = a.devicePixelRatio, N = {}, O = {}, s.DPR = P ||
					1, Q.width = Math.max( a.innerWidth || 0,
					z.clientWidth ), Q.height = Math.max( a.innerHeight || 0,
					z.clientHeight ), Q.vw = Q.width / 100, Q.vh = Q.height /
					100, r = [ Q.height, Q.width, P ].join(
					'-' ), Q.em = s.getEmValue(), Q.rem = Q.em;
			}

			function g( a, b, c, d ) {
				var e, f, g, h;
				return 'saveData' === B.algorithm ? a > 2.7 ? h = c + 1 : ( f = b -
					c, e = Math.pow( a - .6, 1.5 ), g = f * e, d &&
				( g += .1 * e ), h = a + g ) : h = c > 1 ? Math.sqrt( a * b ) : a, h > c;
			}

			function h( a ) {
				var b, c = s.getSet( a ), d = !1;
				'pending' !== c && ( d = r, c &&
				( b = s.setRes( c ), s.applySetCandidate( b,
					a ) ) ), a[ s.ns ].evaled = d;
			}

			function i( a, b ) {return a.res - b.res;}

			function j( a, b, c ) {
				var d;
				return !c && b &&
				( c = a[ s.ns ].sets, c = c && c[ c.length - 1 ] ), d = k( b, c ), d &&
				( b = s.makeUrl(
					b ), a[ s.ns ].curSrc = b, a[ s.ns ].curCan = d, d.res ||
				aa( d, d.set.sizes ) ), d;
			}

			function k( a, b ) {
				var c, d, e;
				if (a && b) {
					for (e = s.parseSet( b ), a = s.makeUrl( a ), c = 0; c <
					e.length; c ++) {
						if (a === s.makeUrl( e[ c ].url )) {
							d = e[ c ];
							break;
						}
					}
				}
				return d;
			}

			function l( a, b ) {
				var c, d, e, f, g = a.getElementsByTagName( 'source' );
				for (c = 0, d = g.length; d >
				c; c ++) {
					e = g[ c ], e[ s.ns ] = !0, f = e.getAttribute(
						'srcset' ), f && b.push( {
						srcset: f,
						media: e.getAttribute( 'media' ),
						type: e.getAttribute( 'type' ),
						sizes: e.getAttribute( 'sizes' ),
					} );
				}
			}

			function m( a, b ) {
				function c( b ) {
					var c, d = b.exec( a.substring( m ) );
					return d ? ( c = d[ 0 ], m += c.length, c ) : void 0;
				}

				function e() {
					var a, c, d, e, f, i, j, k, l, m = !1, o = {};
					for (e = 0; e < h.length; e ++) {
						f = h[ e ], i = f[ f.length -
						1 ], j = f.substring( 0, f.length - 1 ), k = parseInt( j,
							10 ), l = parseFloat( j ), X.test( j ) && 'w' === i ? ( ( a ||
							c ) && ( m = !0 ), 0 === k ? m = !0 : a = k ) : Y.test( j ) &&
						'x' === i
							? ( ( a || c || d ) && ( m = !0 ), 0 > l ? m = !0 : c = l )
							: X.test( j ) && 'h' === i ? ( ( d || c ) && ( m = !0 ), 0 === k
								? m = !0
								: d = k ) : m = !0;
					}
					m || ( o.url = g, a && ( o.w = a ), c && ( o.d = c ), d &&
					( o.h = d ), d || c || a || ( o.d = 1 ), 1 === o.d &&
					( b.has1x = !0 ), o.set = b, n.push( o ) );
				}

				function f() {
					for (c( T ), i = '', j = 'in descriptor'; ;) {
						if (k = a.charAt( m ), 'in descriptor' === j) {
							if (d( k )) {
								i &&
								( h.push( i ), i = '', j = 'after descriptor' );
							}
							else {
								if (',' === k) {
									return m += 1, i && h.push( i ), void e();
								}
								if ('(' === k) {
									i += k, j = 'in parens';
								}
								else {
									if ('' === k) {
										return i && h.push( i ), void e();
									}
									i += k;
								}
							}
						}
						else if ('in parens' === j) {
							if (')' ===
								k) {
								i += k, j = 'in descriptor';
							}
							else {
								if ('' === k) {
									return h.push( i ), void e();
								}
								i += k;
							}
						}
						else if ('after descriptor' === j) {
							if (d( k )) {
								;
							}
							else {
								if ('' === k) {
									return void e();
								}
								j = 'in descriptor', m -= 1;
							}
						}
						m += 1;
					}
				}

				for (var g, h, i, j, k, l = a.length, m = 0, n = []; ;) {
					if (c( U ), m >= l) {
						return n;
					}
					g = c( V ), h = [], ',' === g.slice( - 1 ) ? ( g = g.replace( W,
						'' ), e() ) : f();
				}
			}

			function n( a ) {
				function b( a ) {
					function b() {
						f && ( g.push( f ), f = '' );
					}

					function c() {g[ 0 ] && ( h.push( g ), g = [] );}

					for (var e, f = '', g = [], h = [], i = 0, j = 0, k = !1; ;) {
						if (e = a.charAt( j ), '' === e) {
							return b(), c(), h;
						}
						if (k) {
							if ('*' === e && '/' === a[ j + 1 ]) {
								k = !1, j += 2, b();
								continue;
							}
							j += 1;
						}
						else {
							if (d( e )) {
								if (a.charAt( j - 1 ) && d( a.charAt( j - 1 ) ) || !f) {
									j += 1;
									continue;
								}
								if (0 === i) {
									b(), j += 1;
									continue;
								}
								e = ' ';
							}
							else if ('(' === e) {
								i += 1;
							}
							else if (')' === e) {
								i -= 1;
							}
							else {
								if (',' === e) {
									b(), c(), j += 1;
									continue;
								}
								if ('/' === e && '*' === a.charAt( j + 1 )) {
									k = !0, j += 2;
									continue;
								}
							}
							f += e, j += 1;
						}
					}
				}

				function c( a ) {
					return k.test( a ) && parseFloat( a ) >= 0
						? !0
						: l.test( a ) ? !0 : '0' === a || '-0' === a || '+0' === a ? !0 : !1;
				}

				var e, f, g, h, i, j,
					k = /^(?:[+-]?[0-9]+|[0-9]*\.[0-9]+)(?:[eE][+-]?[0-9]+)?(?:ch|cm|em|ex|in|mm|pc|pt|px|rem|vh|vmin|vmax|vw)$/i,
					l = /^calc\((?:[0-9a-z \.\+\-\*\/\(\)]+)\)$/i;
				for (f = b( a ), g = f.length, e = 0; g >
				e; e ++) {
					if (h = f[ e ], i = h[ h.length - 1 ], c( i )) {
						if (j = i, h.pop(), 0 === h.length) {
							return j;
						}
						if (h = h.join( ' ' ), s.matchesMedia( h )) {
							return j;
						}
					}
				}
				return '100vw';
			}

			b.createElement( 'picture' );
			var o, p, q, r, s = {}, t = !1, u = function() {},
				v = b.createElement( 'img' ), w = v.getAttribute, x = v.setAttribute,
				y = v.removeAttribute, z = b.documentElement, A = {},
				B = { algorithm: '' }, C = 'data-pfsrc', D = C + 'set',
				E = navigator.userAgent,
				F = /rident/.test( E ) || /ecko/.test( E ) && E.match( /rv\:(\d+)/ ) &&
					RegExp.$1 > 35, G = 'currentSrc', H = /\s+\+?\d+(e\d+)?w/,
				I = /(\([^)]+\))?\s*(.+)/, J = a.picturefillCFG,
				K = 'position:absolute;left:0;visibility:hidden;display:block;padding:0;border:none;font-size:1em;width:1em;overflow:hidden;clip:rect(0px, 0px, 0px, 0px)',
				L = 'font-size:100%!important;', M = !0, N = {}, O = {},
				P = a.devicePixelRatio, Q = { px: 1, 'in': 96 },
				R = b.createElement( 'a' ), S = !1, T = /^[ \t\n\r\u000c]+/,
				U = /^[, \t\n\r\u000c]+/, V = /^[^ \t\n\r\u000c]+/, W = /[,]+$/,
				X = /^\d+$/, Y = /^-?(?:[0-9]+|[0-9]*\.[0-9]+)(?:[eE][+-]?[0-9]+)?$/,
				Z = function( a, b, c, d ) {
					a.addEventListener
						? a.addEventListener( b, c, d || !1 )
						: a.attachEvent && a.attachEvent( 'on' + b, c );
				}, $ = function( a ) {
					var b = {};
					return function( c ) {return c in b || ( b[ c ] = a( c ) ), b[ c ];};
				}, _ = function() {
					var a = /^([\d\.]+)(em|vw|px)$/, b = function() {
						for (var a = arguments, b = 0, c = a[ 0 ]; ++ b in a;) {
							c = c.replace(
								a[ b ], a[ ++ b ] );
						}
						return c;
					}, c = $( function( a ) {
						return 'return ' +
							b( ( a || '' ).toLowerCase(), /\band\b/g, '&&', /,/g, '||',
								/min-([a-z-\s]+):/g, 'e.$1>=', /max-([a-z-\s]+):/g, 'e.$1<=',
								/calc([^)]+)/g, '($1)', /(\d+[\.]*[\d]*)([a-z]+)/g, '($1 * e.$2)',
								/^(?!(e.[a-z]|[0-9\.&=|><\+\-\*\(\)\/])).*/gi, '' ) + ';';
					} );
					return function( b, d ) {
						var e;
						if (!( b in N )) {
							if (N[ b ] = !1, d &&
							( e = b.match( a ) )) {
								N[ b ] = e[ 1 ] *
									Q[ e[ 2 ] ];
							}
							else {
								try {N[ b ] = new Function( 'e', c( b ) )( Q );}
								catch ( f ) {}
							}
						}
						return N[ b ];
					};
				}(), aa = function( a, b ) {
					return a.w ? ( a.cWidth = s.calcListLength(
						b || '100vw' ), a.res = a.w / a.cWidth ) : a.res = a.d, a;
				}, ba = function( a ) {
					if (t) {
						var c, d, e, f = a || {};
						if (f.elements && 1 === f.elements.nodeType &&
						( 'IMG' === f.elements.nodeName.toUpperCase()
							? f.elements = [ f.elements ]
							: ( f.context = f.elements, f.elements = null ) ), c = f.elements ||
							s.qsa( f.context || b, f.reevaluate || f.reselect
								? s.sel
								: s.selShort ), e = c.length) {
							for (s.setupRun( f ), S = !0, d = 0; e > d; d ++) {
								s.fillImg( c[ d ],
									f );
							}
							s.teardownRun( f );
						}
					}
				};
			o = a.console && console.warn
				? function( a ) {console.warn( a );}
				: u, G in v ||
			( G = 'src' ), A[ 'image/jpeg' ] = !0, A[ 'image/gif' ] = !0, A[ 'image/png' ] = !0, A[ 'image/svg+xml' ] = b.implementation.hasFeature(
				'http://www.w3.org/TR/SVG11/feature#Image', '1.1' ), s.ns = ( 'pf' +
				( new Date ).getTime() ).substr( 0, 9 ), s.supSrcset = 'srcset' in
				v, s.supSizes = 'sizes' in
				v, s.supPicture = !!a.HTMLPictureElement, s.supSrcset && s.supPicture &&
			!s.supSizes && !function( a ) {
				v.srcset = 'data:,a', a.src = 'data:,a', s.supSrcset = v.complete ===
					a.complete, s.supPicture = s.supSrcset && s.supPicture;
			}( b.createElement( 'img' ) ), s.supSrcset && !s.supSizes
				? !function() {
					var a = 'data:image/gif;base64,R0lGODlhAgABAPAAAP///wAAACH5BAAAAAAALAAAAAACAAEAAAICBAoAOw==',
						c = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==',
						d = b.createElement( 'img' ), e = function() {
							var a = d.width;
							2 === a && ( s.supSizes = !0 ), q = s.supSrcset &&
								!s.supSizes, t = !0, setTimeout( ba );
						};
					d.onload = e, d.onerror = e, d.setAttribute( 'sizes',
						'9px' ), d.srcset = c + ' 1w,' + a + ' 9w', d.src = c;
				}()
				: t = !0, s.selShort = 'picture>img,img[srcset]', s.sel = s.selShort, s.cfg = B, s.DPR = P ||
				1, s.u = Q, s.types = A, s.setSize = u, s.makeUrl = $(
				function( a ) {return R.href = a, R.href;} ), s.qsa = function(
				a, b ) {
				return 'querySelector' in a ? a.querySelectorAll( b ) : [];
			}, s.matchesMedia = function() {
				return a.matchMedia &&
				( matchMedia( '(min-width: 0.1em)' ) || {} ).matches
					? s.matchesMedia = function( a ) {
						return !a || matchMedia( a ).matches;
					}
					: s.matchesMedia = s.mMQ, s.matchesMedia.apply( this, arguments );
			}, s.mMQ = function( a ) {
				return a
					? _( a )
					: !0;
			}, s.calcLength = function( a ) {
				var b = _( a, !0 ) || !1;
				return 0 > b && ( b = !1 ), b;
			}, s.supportsType = function( a ) {
				return a
					? A[ a ]
					: !0;
			}, s.parseSize = $( function( a ) {
				var b = ( a || '' ).match( I );
				return { media: b && b[ 1 ], length: b && b[ 2 ] };
			} ), s.parseSet = function( a ) {
				return a.cands || ( a.cands = m( a.srcset, a ) ), a.cands;
			}, s.getEmValue = function() {
				var a;
				if (!p && ( a = b.body )) {
					var c = b.createElement( 'div' ), d = z.style.cssText,
						e = a.style.cssText;
					c.style.cssText = K, z.style.cssText = L, a.style.cssText = L, a.appendChild(
						c ), p = c.offsetWidth, a.removeChild( c ), p = parseFloat( p,
						10 ), z.style.cssText = d, a.style.cssText = e;
				}
				return p || 16;
			}, s.calcListLength = function( a ) {
				if (!( a in O ) || B.uT) {
					var b = s.calcLength( n( a ) );
					O[ a ] = b ? b : Q.width;
				}
				return O[ a ];
			}, s.setRes = function( a ) {
				var b;
				if (a) {
					b = s.parseSet( a );
					for (var c = 0, d = b.length; d > c; c ++) {
						aa( b[ c ], a.sizes );
					}
				}
				return b;
			}, s.setRes.res = aa, s.applySetCandidate = function(
				a, b ) {
				if (a.length) {
					var c, d, e, f, h, k, l, m, n, o = b[ s.ns ], p = s.DPR;
					if (k = o.curSrc || b[ G ], l = o.curCan ||
						j( b, k, a[ 0 ].set ), l && l.set === a[ 0 ].set &&
					( n = F && !b.complete && l.res - .1 > p, n ||
					( l.cached = !0, l.res >= p && ( h = l ) ) ), !h) {
						for (a.sort(
							i ), f = a.length, h = a[ f - 1 ], d = 0; f >
								 d; d ++) {
							if (c = a[ d ], c.res >=
							p) {
								e = d - 1, h = a[ e ] && ( n || k !== s.makeUrl( c.url ) ) &&
								g( a[ e ].res, c.res, p, a[ e ].cached ) ? a[ e ] : c;
								break;
							}
						}
					}
					h && ( m = s.makeUrl( h.url ), o.curSrc = m, o.curCan = h, m !== k &&
					s.setSrc( b, h ), s.setSize( b ) );
				}
			}, s.setSrc = function( a, b ) {
				var c;
				a.src = b.url, 'image/svg+xml' === b.set.type &&
				( c = a.style.width, a.style.width = a.offsetWidth + 1 +
					'px', a.offsetWidth + 1 && ( a.style.width = c ) );
			}, s.getSet = function( a ) {
				var b, c, d, e = !1, f = a[ s.ns ].sets;
				for (b = 0; b < f.length && !e; b ++) {
					if (c = f[ b ], c.srcset &&
					s.matchesMedia( c.media ) && ( d = s.supportsType( c.type ) )) {
						'pending' === d && ( c = d ), e = c;
						break;
					}
				}
				return e;
			}, s.parseSets = function( a, b, d ) {
				var e, f, g, h, i = b && 'PICTURE' === b.nodeName.toUpperCase(),
					j = a[ s.ns ];
				( j.src === c || d.src ) &&
				( j.src = w.call( a, 'src' ), j.src ? x.call( a, C, j.src ) : y.call( a,
					C ) ), ( j.srcset === c || d.srcset || !s.supSrcset || a.srcset ) &&
				( e = w.call( a, 'srcset' ), j.srcset = e, h = !0 ), j.sets = [], i &&
				( j.pic = !0, l( b, j.sets ) ), j.srcset ? ( f = {
					srcset: j.srcset,
					sizes: w.call( a, 'sizes' ),
				}, j.sets.push( f ), g = ( q || j.src ) &&
					H.test( j.srcset || '' ), g || !j.src || k( j.src, f ) || f.has1x ||
				( f.srcset += ', ' + j.src, f.cands.push(
					{ url: j.src, d: 1, set: f } ) ) ) : j.src && j.sets.push( {
					srcset: j.src,
					sizes: null,
				} ), j.curCan = null, j.curSrc = c, j.supported = !( i || f &&
					!s.supSrcset || g && !s.supSizes ), h && s.supSrcset &&
				!j.supported && ( e ? ( x.call( a, D, e ), a.srcset = '' ) : y.call( a,
					D ) ), j.supported && !j.srcset &&
				( !j.src && a.src || a.src !== s.makeUrl( j.src ) ) && ( null === j.src
					? a.removeAttribute( 'src' )
					: a.src = j.src ), j.parsed = !0;
			}, s.fillImg = function( a, b ) {
				var c, d = b.reselect || b.reevaluate;
				a[ s.ns ] || ( a[ s.ns ] = {} ), c = a[ s.ns ], ( d || c.evaled !==
					r ) && ( ( !c.parsed || b.reevaluate ) &&
				s.parseSets( a, a.parentNode, b ), c.supported ? c.evaled = r : h( a ) );
			}, s.setupRun = function() {
				( !S || M || P !== a.devicePixelRatio ) && f();
			}, s.supPicture ? ( ba = u, s.fillImg = u ) : !function() {
				var c, d = a.attachEvent ? /d$|^c/ : /d$|^c|^i/, e = function() {
					var a = b.readyState || '';
					f = setTimeout( e, 'loading' === a ? 200 : 999 ), b.body &&
					( s.fillImgs(), c = c || d.test( a ), c && clearTimeout( f ) );
				}, f = setTimeout( e, b.body ? 9 : 99 ), g = function( a, b ) {
					var c, d, e = function() {
						var f = new Date - d;
						b > f ? c = setTimeout( e, b - f ) : ( c = null, a() );
					};
					return function() {d = new Date, c || ( c = setTimeout( e, b ) );};
				}, h = z.clientHeight, i = function() {
					M = Math.max( a.innerWidth || 0, z.clientWidth ) !== Q.width ||
						z.clientHeight !== h, h = z.clientHeight, M && s.fillImgs();
				};
				Z( a, 'resize', g( i, 99 ) ), Z( b, 'readystatechange', e );
			}(), s.picturefill = ba, s.fillImgs = ba, s.teardownRun = u, ba._ = s, a.picturefillCFG = {
				pf: s,
				push: function( a ) {
					var b = a.shift();
					'function' == typeof s[ b ]
						? s[ b ].apply( s, a )
						: ( B[ b ] = a[ 0 ], S && s.fillImgs( { reselect: !0 } ) );
				},
			};
			for (; J && J.length;) {
				a.picturefillCFG.push( J.shift() );
			}
			a.picturefill = ba, 'object' == typeof module && 'object' ==
			typeof module.exports ? module.exports = ba : 'function' ==
				typeof define && define.amd &&
				define( 'picturefill', function() {return ba;} ), s.supPicture ||
			( A[ 'image/webp' ] = e( 'image/webp',
				'data:image/webp;base64,UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAABBxAR/Q9ERP8DAABWUDggGAAAADABAJ0BKgEAAQADADQlpAADcAD++/1QAA==' ) );
		}( window, document );/*! picturefill - v3.0.2 - 2016-02-12
 * https://scottjehl.github.io/picturefill/
 * Copyright (c) 2016 https://github.com/scottjehl/picturefill/blob/master/Authors.txt; Licensed MIT
 */
		!function( a ) {
			'use strict';
			var b, c = 0, d = function() {
				window.picturefill && a( window.picturefill ), ( window.picturefill ||
					c > 9999 ) && clearInterval( b ), c ++;
			};
			b = setInterval( d, 8 ), d();
		}( function( a ) {
			'use strict';
			var b = window.document, c = window.Element, d = window.MutationObserver,
				e = function() {}, f = {
					disconnect: e,
					take: e,
					observe: e,
					start: e,
					stop: e,
					connected: !1,
				}, g = /^loade|^c|^i/.test( b.readyState || '' ), h = a._;
			if (h.mutationSupport = !1, h.observer = f, Object.keys &&
			window.HTMLSourceElement && b.addEventListener) {
				var i, j, k, l, m = { src: 1, srcset: 1, sizes: 1, media: 1 },
					n = Object.keys( m ), o = {
						attributes: !0,
						childList: !0,
						subtree: !0,
						attributeFilter: n,
					}, p = c && c.prototype, q = {},
					r = function( a, b ) {q[ a ] = h[ a ], h[ a ] = b;};
				p && !p.matches &&
				( p.matches = p.matchesSelector || p.mozMatchesSelector ||
					p.webkitMatchesSelector || p.msMatchesSelector ), p && p.matches &&
				( i = function( a, b ) {
					return a.matches( b );
				}, h.mutationSupport = !( !Object.create ||
					!Object.defineProperties ) ), h.mutationSupport &&
				( f.observe = function() {
					k && ( f.connected = !0, j && j.observe( b.documentElement, o ) );
				}, f.disconnect = function() {
					f.connected = !1, j && j.disconnect();
				}, f.take = function() {
					j ? h.onMutations( j.takeRecords() ) : l && l.take();
				}, f.start = function() {k = !0, f.observe();}, f.stop = function() {k = !1, f.disconnect();}, r(
					'setupRun', function() {
						return f.disconnect(), q.setupRun.apply( this, arguments );
					} ), r( 'teardownRun', function() {
					var a = q.setupRun.apply( this, arguments );
					return f.observe(), a;
				} ), r( 'setSrc', function() {
					var a, b = f.connected;
					return f.disconnect(), a = q.setSrc.apply( this, arguments ), b &&
					f.observe(), a;
				} ), h.onMutations = function( a ) {
					var b, c, d = [];
					for (b = 0, c = a.length; c > b; b ++) {
						g && 'childList' ===
						a[ b ].type ? h.onSubtreeChange( a[ b ], d ) : 'attributes' ===
							a[ b ].type && h.onAttrChange( a[ b ], d );
					}
					d.length && h.fillImgs( { elements: d, reevaluate: !0 } );
				}, h.onSubtreeChange = function( a, b ) {
					h.findAddedMutations( a.addedNodes, b ), h.findRemovedMutations(
						a.removedNodes, a.target, b );
				}, h.findAddedMutations = function( a, b ) {
					var c, d, e, f;
					for (c = 0, d = a.length; d > c; c ++) {
						e = a[ c ], 1 === e.nodeType &&
						( f = e.nodeName.toUpperCase(), 'PICTURE' === f ? h.addToElements(
							e.getElementsByTagName( 'img' )[ 0 ], b ) : 'IMG' === f &&
						i( e, h.selShort ) ? h.addToElements( e, b ) : 'SOURCE' === f
							? h.addImgForSource( e, e.parentNode, b )
							: h.addToElements( h.qsa( e, h.selShort ), b ) );
					}
				}, h.findRemovedMutations = function( a, b, c ) {
					var d, e, f;
					for (d = 0, e = a.length; e > d; d ++) {
						f = a[ d ], 1 === f.nodeType &&
						'SOURCE' === f.nodeName.toUpperCase() &&
						h.addImgForSource( f, b, c );
					}
				}, h.addImgForSource = function( a, b, c ) {
					b && 'PICTURE' !== ( b.nodeName || '' ).toUpperCase() &&
					( b = b.parentNode, b && 'PICTURE' ===
					( b.nodeName || '' ).toUpperCase() || ( b = null ) ), b &&
					h.addToElements( b.getElementsByTagName( 'img' )[ 0 ], c );
				}, h.addToElements = function( a, b ) {
					var c, d;
					if (a) {
						if ('length' in a && !a.nodeType) {
							for (c = 0, d = a.length; d >
							c; c ++) {
								h.addToElements( a[ c ], b );
							}
						}
						else {
							a.parentNode && - 1 ===
							b.indexOf( a ) && b.push( a );
						}
					}
				}, h.onAttrChange = function( a, b ) {
					var c, d = a.target[ h.ns ];
					d || 'srcset' !== a.attributeName || 'IMG' !==
					( c = a.target.nodeName.toUpperCase() )
						? d && ( c || ( c = a.target.nodeName.toUpperCase() ), 'IMG' === c
						? ( a.attributeName in d &&
						( d[ a.attributeName ] = void 0 ), h.addToElements( a.target, b ) )
						: 'SOURCE' === c &&
						h.addImgForSource( a.target, a.target.parentNode, b ) )
						: h.addToElements( a.target, b );
				}, h.supPicture || ( d && !h.testMutationEvents
					? j = new d( h.onMutations )
					: ( l = function() {
						var a = !1, b = [], c = window.setImmediate || window.setTimeout;
						return function( d ) {
							a || ( a = !0, l.take || ( l.take = function() {
								b.length && ( h.onMutations( b ), b = [] ), a = !1;
							} ), c( l.take ) ), b.push( d );
						};
					}(), b.documentElement.addEventListener( 'DOMNodeInserted',
						function( a ) {
							f.connected && g && l( {
								type: 'childList',
								addedNodes: [ a.target ],
								removedNodes: [],
							} );
						}, !0 ), b.documentElement.addEventListener( 'DOMNodeRemoved',
						function( a ) {
							f.connected && g && 'SOURCE' === ( a.target || {} ).nodeName && l(
								{
									type: 'childList',
									addedNodes: [],
									removedNodes: [ a.target ],
									target: a.target.parentNode,
								} );
						}, !0 ), b.documentElement.addEventListener( 'DOMAttrModified',
						function( a ) {
							f.connected && m[ a.attrName ] && l( {
								type: 'attributes',
								target: a.target,
								attributeName: a.attrName,
							} );
						}, !0 ) ), window.HTMLImageElement && Object.defineProperties &&
				!function() {
					var a = b.createElement( 'img' ), c = [], d = a.getAttribute,
						e = a.setAttribute, f = { src: 1 };
					h.supSrcset && !h.supSizes &&
					( f.srcset = 1 ), Object.defineProperties( HTMLImageElement.prototype,
						{
							getAttribute: {
								value: function( a ) {
									var b;
									return f[ a ] && ( b = this[ h.ns ] ) && void 0 !== b[ a ]
										? b[ a ]
										: d.apply( this, arguments );
								}, writeable: !0, enumerable: !0, configurable: !0,
							},
						} ), h.supSrcset || c.push( 'srcset' ), h.supSizes ||
					c.push( 'sizes' ), c.forEach( function( a ) {
						Object.defineProperty( HTMLImageElement.prototype, a, {
							set: function( b ) {e.call( this, a, b );},
							get: function() {return d.call( this, a ) || '';},
							enumerable: !0,
							configurable: !0,
						} );
					} ), 'currentSrc' in a || !function() {
						var a, c = function( a, b ) {
							null == b && ( b = a.src || '' ), Object.defineProperty( a,
								'pfCurrentSrc', { value: b, writable: !0 } );
						}, d = c;
						h.supSrcset && window.devicePixelRatio && ( a = function( a, b ) {
							var c = a.d || a.w || a.res, d = b.d || b.w || b.res;
							return c - d;
						}, c = function( b ) {
							var c, e, f, g, i = b[ h.ns ];
							if (i && i.supported && i.srcset && i.sets &&
								( e = h.parseSet( i.sets[ 0 ] ) ) && e.sort) {
								for (e.sort( a ), f = e.length, g = e[ f - 1 ], c = 0; f >
								c; c ++) {
									if (e[ c ].d >= window.devicePixelRatio) {
										g = e[ c ];
										break;
									}
								}
								g && ( g = h.makeUrl( g.url ) );
							}
							d( b, g );
						} ), b.addEventListener( 'load', function( a ) {
							'IMG' === a.target.nodeName.toUpperCase() && c( a.target );
						}, !0 ), Object.defineProperty( HTMLImageElement.prototype,
							'currentSrc', {
								set: function() {
									window.console && console.warn &&
									console.warn( 'currentSrc can\'t be set on img element' );
								},
								get: function() {
									return this.complete && c( this ), this.src || this.srcset
										? this.pfCurrentSrc || ''
										: '';
								},
								enumerable: !0,
								configurable: !0,
							} );
					}(), !window.HTMLSourceElement || 'srcset' in
					b.createElement( 'source' ) || [ 'srcset', 'sizes' ].forEach(
						function( a ) {
							Object.defineProperty( window.HTMLSourceElement.prototype, a, {
								set: function( b ) {this.setAttribute( a, b );},
								get: function() {return this.getAttribute( a ) || '';},
								enumerable: !0,
								configurable: !0,
							} );
						} );
				}(), f.start() ), g ||
				b.addEventListener( 'DOMContentLoaded', function() {g = !0;} ) );
			}
		} );
	}

	if (!( 'defineProperty' in Object && function() {
			try {
				var e = {};
				return Object.defineProperty( e, 'test', { value: 42 } ), !0;
			}
			catch ( t ) {return !1;}
		}()
	)) {

// Object.defineProperty
		( function( nativeDefineProperty ) {

			var supportsAccessors = Object.prototype.hasOwnProperty(
				'__defineGetter__' );
			var ERR_ACCESSORS_NOT_SUPPORTED = 'Getters & setters cannot be defined on this javascript engine';
			var ERR_VALUE_ACCESSORS = 'A property cannot both have accessors and be writable or have a value';

			// Polyfill.io - This does not use CreateMethodProperty because our
			// CreateMethodProperty function uses Object.defineProperty.
			Object[ 'defineProperty' ] = function defineProperty(
				object, property, descriptor ) {

				// Where native support exists, assume it
				if (nativeDefineProperty &&
					( object === window || object === document || object ===
						Element.prototype || object instanceof Element )) {
					return nativeDefineProperty( object, property, descriptor );
				}

				if (object === null ||
					!( object instanceof Object || typeof object === 'object' )) {
					throw new TypeError( 'Object.defineProperty called on non-object' );
				}

				if (!( descriptor instanceof Object )) {
					throw new TypeError( 'Property description must be an object' );
				}

				var propertyString = String( property );
				var hasValueOrWritable = 'value' in descriptor || 'writable' in
					descriptor;
				var getterType = 'get' in descriptor && typeof descriptor.get;
				var setterType = 'set' in descriptor && typeof descriptor.set;

				// handle descriptor.get
				if (getterType) {
					if (getterType !== 'function') {
						throw new TypeError( 'Getter must be a function' );
					}
					if (!supportsAccessors) {
						throw new TypeError( ERR_ACCESSORS_NOT_SUPPORTED );
					}
					if (hasValueOrWritable) {
						throw new TypeError( ERR_VALUE_ACCESSORS );
					}
					Object.__defineGetter__.call( object, propertyString,
						descriptor.get );
				}
				else {
					object[ propertyString ] = descriptor.value;
				}

				// handle descriptor.set
				if (setterType) {
					if (setterType !== 'function') {
						throw new TypeError( 'Setter must be a function' );
					}
					if (!supportsAccessors) {
						throw new TypeError( ERR_ACCESSORS_NOT_SUPPORTED );
					}
					if (hasValueOrWritable) {
						throw new TypeError( ERR_VALUE_ACCESSORS );
					}
					Object.__defineSetter__.call( object, propertyString,
						descriptor.set );
				}

				// OK to define value unconditionally - if a getter has been
				// specified as well, an error would be thrown above
				if ('value' in descriptor) {
					object[ propertyString ] = descriptor.value;
				}

				return object;
			};
		}( Object.defineProperty ) );

	}

// _ESAbstract.CreateDataProperty
// 7.3.4. CreateDataProperty ( O, P, V )
// NOTE
// This abstract operation creates a property whose attributes are set to the
// same defaults used for properties created by the ECMAScript language
// assignment operator. Normally, the property will not already exist. If it
// does exist and is not configurable or if O is not extensible,
// [[DefineOwnProperty]] will return false.
	function CreateDataProperty( O, P, V ) { // eslint-disable-line no-unused-vars
		// 1. Assert: Type(O) is Object.
		// 2. Assert: IsPropertyKey(P) is true.
		// 3. Let newDesc be the PropertyDescriptor{ [[Value]]: V,
		// [[Writable]]: true, [[Enumerable]]: true, [[Configurable]]: true }.
		var newDesc = {
			value: V,
			writable: true,
			enumerable: true,
			configurable: true,
		};
		// 4. Return ? O.[[DefineOwnProperty]](P, newDesc).
		try {
			Object.defineProperty( O, P, newDesc );
			return true;
		}
		catch ( e ) {
			return false;
		}
	}

// _ESAbstract.CreateDataPropertyOrThrow
	/* global CreateDataProperty */

// 7.3.6. CreateDataPropertyOrThrow ( O, P, V )
	function CreateDataPropertyOrThrow( O, P, V ) { // eslint-disable-line no-unused-vars
		// 1. Assert: Type(O) is Object.
		// 2. Assert: IsPropertyKey(P) is true.
		// 3. Let success be ? CreateDataProperty(O, P, V).
		var success = CreateDataProperty( O, P, V );
		// 4. If success is false, throw a TypeError exception.
		if (!success) {
			throw new TypeError(
				'Cannot assign value `' + Object.prototype.toString.call( V ) +
				'` to property `' + Object.prototype.toString.call( P ) +
				'` on object `' + Object.prototype.toString.call( O ) + '`' );
		}
		// 5. Return success.
		return success;
	}

// _ESAbstract.CreateMethodProperty
// 7.3.5. CreateMethodProperty ( O, P, V )
	function CreateMethodProperty( O, P, V ) { // eslint-disable-line no-unused-vars
		// 1. Assert: Type(O) is Object.
		// 2. Assert: IsPropertyKey(P) is true.
		// 3. Let newDesc be the PropertyDescriptor{[[Value]]: V, [[Writable]]:
		// true, [[Enumerable]]: false, [[Configurable]]: true}.
		var newDesc = {
			value: V,
			writable: true,
			enumerable: false,
			configurable: true,
		};
		// 4. Return ? O.[[DefineOwnProperty]](P, newDesc).
		Object.defineProperty( O, P, newDesc );
	}

	if (!( 'isArray' in Array
	)) {

// Array.isArray
		/* global CreateMethodProperty, IsArray */
// 22.1.2.2. Array.isArray ( arg )
		CreateMethodProperty( Array, 'isArray', function isArray( arg ) {
			// 1. Return ? IsArray(arg).
			return IsArray( arg );
		} );

	}

	if (!( 'forEach' in Array.prototype
	)) {

// Array.prototype.forEach
		/* global Call, CreateMethodProperty, Get, HasProperty, IsCallable, ToLength, ToObject, ToString */
// 22.1.3.10. Array.prototype.forEach ( callbackfn [ , thisArg ] )
		CreateMethodProperty( Array.prototype, 'forEach',
			function forEach( callbackfn /* [ , thisArg ] */ ) {
				// 1. Let O be ? ToObject(this value).
				var O = ToObject( this );
				// Polyfill.io - If O is a String object, split it into an
				// array in order to iterate correctly. We will use arrayLike
				// in place of O when we are iterating through the list.
				var arraylike = O instanceof String ? O.split( '' ) : O;
				// 2. Let len be ? ToLength(? Get(O, "length")).
				var len = ToLength( Get( O, 'length' ) );
				// 3. If IsCallable(callbackfn) is false, throw a TypeError
				// exception.
				if (IsCallable( callbackfn ) === false) {
					throw new TypeError( callbackfn + ' is not a function' );
				}
				// 4. If thisArg is present, let T be thisArg; else let T be
				// undefined.
				var T = arguments.length > 1 ? arguments[ 1 ] : undefined;
				// 5. Let k be 0.
				var k = 0;
				// 6. Repeat, while k < len
				while (k < len) {
					// a. Let Pk be ! ToString(k).
					var Pk = ToString( k );
					// b. Let kPresent be ? HasProperty(O, Pk).
					var kPresent = HasProperty( arraylike, Pk );
					// c. If kPresent is true, then
					if (kPresent) {
						// i. Let kValue be ? Get(O, Pk).
						var kValue = Get( arraylike, Pk );
						// ii. Perform ? Call(callbackfn, T, « kValue, k, O »).
						Call( callbackfn, T, [ kValue, k, O ] );
					}
					// d. Increase k by 1.
					k = k + 1;
				}
				// 7. Return undefined.
				return undefined;
			} );

	}

	if (!( 'bind' in Function.prototype
	)) {

// Function.prototype.bind
		/* global CreateMethodProperty, IsCallable */
// 19.2.3.2. Function.prototype.bind ( thisArg, ...args )
// https://github.com/es-shims/es5-shim/blob/d6d7ff1b131c7ba14c798cafc598bb6780d37d3b/es5-shim.js#L182
		CreateMethodProperty( Function.prototype, 'bind', function bind( that ) { // .length is 1
			// add necessary es5-shim utilities
			var $Array = Array;
			var $Object = Object;
			var ArrayPrototype = $Array.prototype;
			var Empty = function Empty() { };
			var array_slice = ArrayPrototype.slice;
			var array_concat = ArrayPrototype.concat;
			var array_push = ArrayPrototype.push;
			var max = Math.max;
			// /add necessary es5-shim utilities

			// 1. Let Target be the this value.
			var target = this;
			// 2. If IsCallable(Target) is false, throw a TypeError exception.
			if (!IsCallable( target )) {
				throw new TypeError(
					'Function.prototype.bind called on incompatible ' + target );
			}
			// 3. Let A be a new (possibly empty) internal list of all of the
			//   argument values provided after thisArg (arg1, arg2 etc), in
			// order. XXX slicedArgs will stand in for "A" if used
			var args = array_slice.call( arguments, 1 ); // for normal call
			// 4. Let F be a new native ECMAScript object.
			// 11. Set the [[Prototype]] internal property of F to the standard
			//   built-in Function prototype object as specified in 15.3.3.1.
			// 12. Set the [[Call]] internal property of F as described in
			//   15.3.4.5.1.
			// 13. Set the [[Construct]] internal property of F as described in
			//   15.3.4.5.2.
			// 14. Set the [[HasInstance]] internal property of F as described
			// in 15.3.4.5.3.
			var bound;
			var binder = function() {

				if (this instanceof bound) {
					// 15.3.4.5.2 [[Construct]]
					// When the [[Construct]] internal method of a function
					// object, F that was created using the bind function is
					// called with a list of arguments ExtraArgs, the following
					// steps are taken: 1. Let target be the value of F's
					// [[TargetFunction]] internal property. 2. If target has
					// no [[Construct]] internal method, a TypeError exception
					// is thrown. 3. Let boundArgs be the value of F's
					// [[BoundArgs]] internal property. 4. Let args be a new
					// list containing the same values as the list boundArgs in
					// the same order followed by the same values as the list
					// ExtraArgs in the same order. 5. Return the result of calling the [[Construct]] internal method of target providing args as the arguments.

					var result = target.apply(
						this,
						array_concat.call( args, array_slice.call( arguments ) ),
					);
					if ($Object( result ) === result) {
						return result;
					}
					return this;

				}
				else {
					// 15.3.4.5.1 [[Call]]
					// When the [[Call]] internal method of a function object,
					// F, which was created using the bind function is called
					// with a this value and a list of arguments ExtraArgs, the
					// following steps are taken: 1. Let boundArgs be the value
					// of F's [[BoundArgs]] internal property. 2. Let boundThis
					// be the value of F's [[BoundThis]] internal property. 3.
					// Let target be the value of F's [[TargetFunction]]
					// internal property. 4. Let args be a new list containing
					// the same values as the list boundArgs in the same order
					// followed by the same values as the list ExtraArgs in the
					// same order. 5. Return the result of calling the [[Call]] internal method of target providing boundThis as the this value and providing args as the arguments.

					// equiv: target.call(this, ...boundArgs, ...args)
					return target.apply(
						that,
						array_concat.call( args, array_slice.call( arguments ) ),
					);

				}

			};

			// 15. If the [[Class]] internal property of Target is "Function",
			// then a. Let L be the length property of Target minus the length
			// of A. b. Set the length own property of F to either 0 or L,
			// whichever is larger. 16. Else set the length own property of F
			// to 0.

			var boundLength = max( 0, target.length - args.length );

			// 17. Set the attributes of the length own property of F to the
			// values specified in 15.3.5.1.
			var boundArgs = [];
			for (var i = 0; i < boundLength; i ++) {
				array_push.call( boundArgs, '$' + i );
			}

			// XXX Build a dynamic function with desired amount of arguments is
			// the only way to set the length property of a function. In
			// environments where Content Security Policies enabled (Chrome
			// extensions, for ex.) all use of eval or Function costructor
			// throws an exception. However in all of these environments
			// Function.prototype.bind exists and so this code will never be
			// executed.
			bound = Function( 'binder', 'return function (' + boundArgs.join( ',' ) +
				'){ return binder.apply(this, arguments); }' )( binder );

			if (target.prototype) {
				Empty.prototype = target.prototype;
				bound.prototype = new Empty();
				// Clean up dangling references.
				Empty.prototype = null;
			}

			// TODO
			// 18. Set the [[Extensible]] internal property of F to true.

			// TODO
			// 19. Let thrower be the [[ThrowTypeError]] function Object
			// (13.2.3). 20. Call the [[DefineOwnProperty]] internal method of
			// F with arguments "caller", PropertyDescriptor {[[Get]]: thrower,
			// [[Set]]: thrower, [[Enumerable]]: false, [[Configurable]]:
			// false}, and false. 21. Call the [[DefineOwnProperty]] internal
			// method of F with arguments "arguments", PropertyDescriptor
			// {[[Get]]: thrower, [[Set]]: thrower, [[Enumerable]]: false,
			// [[Configurable]]: false}, and false.

			// TODO
			// NOTE Function objects created using Function.prototype.bind do
			// not have a prototype property or the [[Code]],
			// [[FormalParameters]], and [[Scope]] internal properties. XXX
			// can't delete prototype in pure-js.

			// 22. Return F.
			return bound;
		} );

	}

	if (!( 'freeze' in Object
	)) {

// Object.freeze
		/* global CreateMethodProperty */
// 19.1.2.6. Object.freeze ( O )
		CreateMethodProperty( Object, 'freeze', function freeze( O ) {
			// This feature cannot be implemented fully as a polyfill.
			// We choose to silently fail which allows "securable" code
			// to "gracefully" degrade to working but insecure code.
			return O;
		} );

	}

	if (!( 'getOwnPropertyDescriptor' in Object && 'function' ==
		typeof Object.getOwnPropertyDescriptor && function() {
			try {
				var t = {};
				return t.test = 0, 0 ===
				Object.getOwnPropertyDescriptor( t, 'test' ).value;
			}
			catch ( e ) {return !1;}
		}()
	)) {

// Object.getOwnPropertyDescriptor
		/* global CreateMethodProperty */
		( function() {
			var call = Function.prototype.call;
			var prototypeOfObject = Object.prototype;
			var owns = call.bind( prototypeOfObject.hasOwnProperty );

			var lookupGetter;
			var lookupSetter;
			var supportsAccessors;
			if (( supportsAccessors = owns( prototypeOfObject,
				'__defineGetter__' ) )) {
				lookupGetter = call.bind( prototypeOfObject.__lookupGetter__ );
				lookupSetter = call.bind( prototypeOfObject.__lookupSetter__ );
			}

			function doesGetOwnPropertyDescriptorWork( object ) {
				try {
					object.sentinel = 0;
					return Object.getOwnPropertyDescriptor(
						object,
						'sentinel',
					).value === 0;
				}
				catch ( exception ) {
					// returns falsy
				}
			}

			// check whether getOwnPropertyDescriptor works if it's given.
			// Otherwise, shim partially.
			if (Object.defineProperty) {
				var getOwnPropertyDescriptorWorksOnObject =
					doesGetOwnPropertyDescriptorWork( {} );
				var getOwnPropertyDescriptorWorksOnDom = typeof document ==
					'undefined' ||
					doesGetOwnPropertyDescriptorWork( document.createElement( 'div' ) );
				if (!getOwnPropertyDescriptorWorksOnDom ||
					!getOwnPropertyDescriptorWorksOnObject
				) {
					var getOwnPropertyDescriptorFallback = Object.getOwnPropertyDescriptor;
				}
			}

			if (!Object.getOwnPropertyDescriptor ||
				getOwnPropertyDescriptorFallback) {
				var ERR_NON_OBJECT = 'Object.getOwnPropertyDescriptor called on a non-object: ';

				CreateMethodProperty( Object, 'getOwnPropertyDescriptor',
					function getOwnPropertyDescriptor( object, property ) {
						if (( typeof object != 'object' && typeof object != 'function' ) ||
							object === null) {
							throw new TypeError( ERR_NON_OBJECT + object );
						}

						// make a valiant attempt to use the real
						// getOwnPropertyDescriptor for I8's DOM elements.
						if (getOwnPropertyDescriptorFallback) {
							try {
								return getOwnPropertyDescriptorFallback.call( Object, object,
									property );
							}
							catch ( exception ) {
								// try the shim if the real one doesn't work
							}
						}

						// If object does not owns property return undefined
						// immediately.
						if (!owns( object, property )) {
							return;
						}

						// If object has a property then it's for sure both
						// `enumerable` and `configurable`.
						var descriptor = { enumerable: true, configurable: true };

						// If JS engine supports accessor properties then
						// property may be a getter or setter.
						if (supportsAccessors) {
							// Unfortunately `__lookupGetter__` will return a
							// getter even if object has own non getter
							// property along with a same named inherited
							// getter. To avoid misbehavior we temporary remove
							// `__proto__` so that `__lookupGetter__` will
							// return getter only if it's owned by an object.
							var prototype = object.__proto__;
							object.__proto__ = prototypeOfObject;

							var getter = lookupGetter( object, property );
							var setter = lookupSetter( object, property );

							// Once we have getter and setter we can put values
							// back.
							object.__proto__ = prototype;

							if (getter || setter) {
								if (getter) {
									descriptor.get = getter;
								}
								if (setter) {
									descriptor.set = setter;
								}
								// If it was accessor property we're done and
								// return here in order to avoid adding `value`
								// to the descriptor.
								return descriptor;
							}
						}

						// If we got this far we know that object has an own
						// property that is not an accessor so we set it as a
						// value and return descriptor.
						descriptor.value = object[ property ];
						descriptor.writable = true;
						return descriptor;
					} );
			}
		}() );

	}

	if (!( 'getOwnPropertyNames' in Object
	)) {

// Object.getOwnPropertyNames
		/* global CreateMethodProperty */

		var toString = ( {} ).toString;
		var split = ''.split;

		CreateMethodProperty( Object, 'getOwnPropertyNames',
			function getOwnPropertyNames( object ) {
				var buffer = [];
				var key;

				// Non-enumerable properties cannot be discovered but can be
				// checked for by name. Define those used internally by JS to
				// allow an incomplete solution
				var commonProps = [
					'length',
					'name',
					'arguments',
					'caller',
					'prototype',
					'observe',
					'unobserve' ];

				if (typeof object === 'undefined' || object === null) {
					throw new TypeError( 'Cannot convert undefined or null to object' );
				}

				// Polyfill.io fallback for non-array-like strings which exist
				// in some ES3 user-agents (IE 8)
				object = toString.call( object ) == '[object String]' ? split.call(
					object, '' ) : Object( object );

				// Enumerable properties only
				for (key in object) {
					if (Object.prototype.hasOwnProperty.call( object, key )) {
						buffer.push( key );
					}
				}

				// Check for and add the common non-enumerable properties
				for (var i = 0, s = commonProps.length; i < s; i ++) {
					if (commonProps[ i ] in object) {
						buffer.push( commonProps[ i ] );
					}
				}

				return buffer;
			} );

	}

	if (!( 'getPrototypeOf' in Object
	)) {

// Object.getPrototypeOf
		/* global CreateMethodProperty */
// Based on: https://github.com/es-shims/es5-shim/blob/master/es5-sham.js

// https://github.com/es-shims/es5-shim/issues#issue/2
// http://ejohn.org/blog/objectgetprototypeof/
// recommended by fschaefer on github
//
// sure, and webreflection says ^_^
// ... this will nerever possibly return null
// ... Opera Mini breaks here with infinite loops
		CreateMethodProperty( Object, 'getPrototypeOf',
			function getPrototypeOf( object ) {
				if (object !== Object( object )) {
					throw new TypeError( 'Object.getPrototypeOf called on non-object' );
				}
				var proto = object.__proto__;
				if (proto || proto === null) {
					return proto;
				}
				else if (typeof object.constructor == 'function' && object instanceof
					object.constructor) {
					return object.constructor.prototype;
				}
				else if (object instanceof Object) {
					return Object.prototype;
				}
				else {
					// Correctly return null for Objects created with
					// `Object.create(null)` (shammed or native) or `{
					// __proto__: null}`.  Also returns null for cross-realm
					// objects on browsers that lack `__proto__` support (like
					// IE <11), but that's the best we can do.
					return null;
				}
			} );

	}

	if (!( 'keys' in Object &&
		function() {return 2 === Object.keys( arguments ).length;}( 1, 2 ) &&
		function() {
			try {return Object.keys( '' ), !0;}
			catch ( t ) {return !1;}
		}()
	)) {

// Object.keys
		/* global CreateMethodProperty */
		CreateMethodProperty( Object, 'keys', ( function() {
			'use strict';

			// modified from https://github.com/es-shims/object-keys

			var has = Object.prototype.hasOwnProperty;
			var toStr = Object.prototype.toString;
			var isEnumerable = Object.prototype.propertyIsEnumerable;
			var hasDontEnumBug = !isEnumerable.call( { toString: null }, 'toString' );
			var hasProtoEnumBug = isEnumerable.call( function() {}, 'prototype' );
			var dontEnums = [
				'toString',
				'toLocaleString',
				'valueOf',
				'hasOwnProperty',
				'isPrototypeOf',
				'propertyIsEnumerable',
				'constructor',
			];
			var equalsConstructorPrototype = function( o ) {
				var ctor = o.constructor;
				return ctor && ctor.prototype === o;
			};
			var excludedKeys = {
				$console: true,
				$external: true,
				$frame: true,
				$frameElement: true,
				$frames: true,
				$innerHeight: true,
				$innerWidth: true,
				$outerHeight: true,
				$outerWidth: true,
				$pageXOffset: true,
				$pageYOffset: true,
				$parent: true,
				$scrollLeft: true,
				$scrollTop: true,
				$scrollX: true,
				$scrollY: true,
				$self: true,
				$webkitIndexedDB: true,
				$webkitStorageInfo: true,
				$window: true,
			};
			var hasAutomationEqualityBug = ( function() {
				/* global window */
				if (typeof window === 'undefined') { return false; }
				for (var k in window) {
					try {
						if (!excludedKeys[ '$' + k ] && has.call( window, k ) &&
							window[ k ] !== null && typeof window[ k ] === 'object') {
							try {
								equalsConstructorPrototype( window[ k ] );
							}
							catch ( e ) {
								return true;
							}
						}
					}
					catch ( e ) {
						return true;
					}
				}
				return false;
			}() );
			var equalsConstructorPrototypeIfNotBuggy = function( o ) {
				/* global window */
				if (typeof window === 'undefined' || !hasAutomationEqualityBug) {
					return equalsConstructorPrototype( o );
				}
				try {
					return equalsConstructorPrototype( o );
				}
				catch ( e ) {
					return false;
				}
			};

			function isArgumentsObject( value ) {
				var str = toStr.call( value );
				var isArgs = str === '[object Arguments]';
				if (!isArgs) {
					isArgs = str !== '[object Array]' &&
						value !== null &&
						typeof value === 'object' &&
						typeof value.length === 'number' &&
						value.length >= 0 &&
						toStr.call( value.callee ) === '[object Function]';
				}
				return isArgs;
			}

			return function keys( object ) {
				var isFunction = toStr.call( object ) === '[object Function]';
				var isArguments = isArgumentsObject( object );
				var isString = toStr.call( object ) === '[object String]';
				var theKeys = [];

				if (object === undefined || object === null) {
					throw new TypeError( 'Cannot convert undefined or null to object' );
				}

				var skipProto = hasProtoEnumBug && isFunction;
				if (isString && object.length > 0 && !has.call( object, 0 )) {
					for (var i = 0; i < object.length; ++ i) {
						theKeys.push( String( i ) );
					}
				}

				if (isArguments && object.length > 0) {
					for (var j = 0; j < object.length; ++ j) {
						theKeys.push( String( j ) );
					}
				}
				else {
					for (var name in object) {
						if (!( skipProto && name === 'prototype' ) &&
							has.call( object, name )) {
							theKeys.push( String( name ) );
						}
					}
				}

				if (hasDontEnumBug) {
					var skipConstructor = equalsConstructorPrototypeIfNotBuggy( object );

					for (var k = 0; k < dontEnums.length; ++ k) {
						if (!( skipConstructor && dontEnums[ k ] === 'constructor' ) &&
							has.call( object, dontEnums[ k ] )) {
							theKeys.push( dontEnums[ k ] );
						}
					}
				}
				return theKeys;
			};
		}() ) );

	}

	if (!( 'assign' in Object
	)) {

// Object.assign
		/* global CreateMethodProperty, Get, ToObject */
// 19.1.2.1 Object.assign ( target, ...sources )
		CreateMethodProperty( Object, 'assign', function assign( target, source ) { // eslint-disable-line no-unused-vars
			// 1. Let to be ? ToObject(target).
			var to = ToObject( target );

			// 2. If only one argument was passed, return to.
			if (arguments.length === 1) {
				return to;
			}

			// 3. Let sources be the List of argument values starting with the
			// second argument
			var sources = Array.prototype.slice.call( arguments, 1 );

			// 4. For each element nextSource of sources, in ascending index
			// order, do
			var index1;
			var index2;
			var keys;
			var from;
			for (index1 = 0; index1 < sources.length; index1 ++) {
				var nextSource = sources[ index1 ];
				// a. If nextSource is undefined or null, let keys be a new
				// empty List.
				if (nextSource === undefined || nextSource === null) {
					keys = [];
					// b. Else,
				}
				else {
					// i. Let from be ! ToObject(nextSource).
					from = ToObject( nextSource );
					// ii. Let keys be ? from.[[OwnPropertyKeys]]().
					/*
					This step in our polyfill is not complying with the specification.
					[[OwnPropertyKeys]] is meant to return ALL keys, including non-enumerable and symbols.
					TODO: When we have Reflect.ownKeys, use that instead as it is the userland equivalent of [[OwnPropertyKeys]].
				*/
					keys = Object.keys( from );
				}

				// c. For each element nextKey of keys in List order, do
				for (index2 = 0; index2 < keys.length; index2 ++) {
					var nextKey = keys[ index2 ];
					var enumerable;
					try {
						// i. Let desc be ? from.[[GetOwnProperty]](nextKey).
						var desc = Object.getOwnPropertyDescriptor( from, nextKey );
						// ii. If desc is not undefined and desc.[[Enumerable]]
						// is true, then
						enumerable = desc !== undefined && desc.enumerable === true;
					}
					catch ( e ) {
						// Polyfill.io - We use
						// Object.prototype.propertyIsEnumerable as a fallback
						// because
						// `Object.getOwnPropertyDescriptor(window.location,
						// 'hash')` causes Internet Explorer 11 to crash.
						enumerable = Object.prototype.propertyIsEnumerable.call( from,
							nextKey );
					}
					if (enumerable) {
						// 1. Let propValue be ? Get(from, nextKey).
						var propValue = Get( from, nextKey );
						// 2. Perform ? Set(to, nextKey, propValue, true).
						to[ nextKey ] = propValue;
					}
				}
			}
			// 5. Return to.
			return to;
		} );

	}

	if (!( 'defineProperties' in Object
	)) {

// Object.defineProperties
		/* global CreateMethodProperty, Get, ToObject, Type */
// 19.1.2.3. Object.defineProperties ( O, Properties )
		CreateMethodProperty( Object, 'defineProperties',
			function defineProperties( O, Properties ) {
				// 1. If Type(O) is not Object, throw a TypeError exception.
				if (Type( O ) !== 'object') {
					throw new TypeError( 'Object.defineProperties called on non-object' );
				}
				// 2. Let props be ? ToObject(Properties).
				var props = ToObject( Properties );
				// 3. Let keys be ? props.[[OwnPropertyKeys]]().
				/*
			Polyfill.io - This step in our polyfill is not complying with the specification.
			[[OwnPropertyKeys]] is meant to return ALL keys, including non-enumerable and symbols.
			TODO: When we have Reflect.ownKeys, use that instead as it is the userland equivalent of [[OwnPropertyKeys]].
		*/
				var keys = Object.keys( props );
				// 4. Let descriptors be a new empty List.
				var descriptors = [];
				// 5. For each element nextKey of keys in List order, do
				for (var i = 0; i < keys.length; i ++) {
					var nextKey = keys[ i ];
					// a. Let propDesc be ? props.[[GetOwnProperty]](nextKey).
					var propDesc = Object.getOwnPropertyDescriptor( props, nextKey );
					// b. If propDesc is not undefined and
					// propDesc.[[Enumerable]] is true, then
					if (propDesc !== undefined && propDesc.enumerable) {
						// i. Let descObj be ? Get(props, nextKey).
						var descObj = Get( props, nextKey );
						// ii. Let desc be ? ToPropertyDescriptor(descObj).
						// Polyfill.io - We skip this step because
						// Object.defineProperty deals with it. TODO: Implement
						// this step?
						var desc = descObj;
						// iii. Append the pair (a two element List) consisting
						// of nextKey and desc to the end of descriptors.
						descriptors.push( [ nextKey, desc ] );
					}
				}
				// 6. For each pair from descriptors in list order, do
				for (var i = 0; i < descriptors.length; i ++) {
					// a. Let P be the first element of pair.
					var P = descriptors[ i ][ 0 ];
					// b. Let desc be the second element of pair.
					var desc = descriptors[ i ][ 1 ];
					// c. Perform ? DefinePropertyOrThrow(O, P, desc).
					Object.defineProperty( O, P, desc );
				}
				// 7. Return O.
				return O;
			} );

	}

	if (!( 'create' in Object
	)) {

// Object.create
		/* global CreateMethodProperty, Type */
		CreateMethodProperty( Object, 'create', function create( O, properties ) {
			// 1. If Type(O) is neither Object nor Null, throw a TypeError
			// exception.
			if (Type( O ) !== 'object' && Type( O ) !== 'null') {
				throw new TypeError( 'Object prototype may only be an Object or null' );
			}
			// 2. Let obj be ObjectCreate(O).
			var obj = new Function( 'e',
				'function Object() {}Object.prototype=e;return new Object' )( O );

			obj.constructor.prototype = O;

			// 3. If Properties is not undefined, then
			if (1 in arguments) {
				// a. Return ? ObjectDefineProperties(obj, Properties).
				return Object.defineProperties( obj, properties );
			}

			return obj;
		} );

	}

// _ESAbstract.OrdinaryCreateFromConstructor
	/* global GetPrototypeFromConstructor */

// 9.1.13. OrdinaryCreateFromConstructor ( constructor, intrinsicDefaultProto [
// , internalSlotsList ] )
	function OrdinaryCreateFromConstructor( constructor, intrinsicDefaultProto ) { // eslint-disable-line no-unused-vars
		var internalSlotsList = arguments[ 2 ] || {};
		// 1. Assert: intrinsicDefaultProto is a String value that is this
		// specification's name of an intrinsic object. The corresponding
		// object must be an intrinsic that is intended to be used as
		// the[[Prototype]] value of an object.

		// 2. Let proto be ? GetPrototypeFromConstructor(constructor,
		// intrinsicDefaultProto).
		var proto = GetPrototypeFromConstructor( constructor,
			intrinsicDefaultProto );

		// 3. Return ObjectCreate(proto, internalSlotsList).
		// Polyfill.io - We do not pass internalSlotsList to Object.create
		// because Object.create does not use the default ordinary object
		// definitions specified in 9.1.
		var obj = Object.create( proto );
		for (var name in internalSlotsList) {
			if (Object.prototype.hasOwnProperty.call( internalSlotsList, name )) {
				Object.defineProperty( obj, name, {
					configurable: true,
					enumerable: false,
					writable: true,
					value: internalSlotsList[ name ],
				} );
			}
		}
		return obj;
	}

// _ESAbstract.Construct
	/* global IsConstructor, OrdinaryCreateFromConstructor, Call */

// 7.3.13. Construct ( F [ , argumentsList [ , newTarget ]] )
	function Construct( F /* [ , argumentsList [ , newTarget ]] */ ) { // eslint-disable-line no-unused-vars
		// 1. If newTarget is not present, set newTarget to F.
		var newTarget = arguments.length > 2 ? arguments[ 2 ] : F;

		// 2. If argumentsList is not present, set argumentsList to a new empty
		// List.
		var argumentsList = arguments.length > 1 ? arguments[ 1 ] : [];

		// 3. Assert: IsConstructor(F) is true.
		if (!IsConstructor( F )) {
			throw new TypeError( 'F must be a constructor.' );
		}

		// 4. Assert: IsConstructor(newTarget) is true.
		if (!IsConstructor( newTarget )) {
			throw new TypeError( 'newTarget must be a constructor.' );
		}

		// 5. Return ? F.[[Construct]](argumentsList, newTarget).
		// Polyfill.io - If newTarget is the same as F, it is equivalent to new
		// F(...argumentsList).
		if (newTarget === F) {
			return new ( Function.prototype.bind.apply( F,
				[ null ].concat( argumentsList ) ) )();
		}
		else {
			// Polyfill.io - This is mimicking section 9.2.2 step 5.a.
			var obj = OrdinaryCreateFromConstructor( newTarget, Object.prototype );
			return Call( F, obj, argumentsList );
		}
	}

// _ESAbstract.ArraySpeciesCreate
	/* global IsArray, ArrayCreate, Get, Type, IsConstructor, Construct */

// 9.4.2.3. ArraySpeciesCreate ( originalArray, length )
	function ArraySpeciesCreate( originalArray, length ) { // eslint-disable-line no-unused-vars
		// 1. Assert: length is an integer Number ≥ 0.
		// 2. If length is -0, set length to +0.
		if (1 / length === - Infinity) {
			length = 0;
		}

		// 3. Let isArray be ? IsArray(originalArray).
		var isArray = IsArray( originalArray );

		// 4. If isArray is false, return ? ArrayCreate(length).
		if (isArray === false) {
			return ArrayCreate( length );
		}

		// 5. Let C be ? Get(originalArray, "constructor").
		var C = Get( originalArray, 'constructor' );

		// Polyfill.io - We skip this section as not sure how to make a
		// cross-realm normal Array, a same-realm Array. 6. If IsConstructor(C)
		// is true, then if (IsConstructor(C)) { a. Let thisRealm be the
		// current Realm Record. b. Let realmC be ? GetFunctionRealm(C). c. If
		// thisRealm and realmC are not the same Realm Record, then i. If
		// SameValue(C, realmC.[[Intrinsics]].[[%Array%]]) is true, set C to
		// undefined. } 7. If Type(C) is Object, then
		if (Type( C ) === 'object') {
			// a. Set C to ? Get(C, @@species).
			C = 'Symbol' in this && 'species' in this.Symbol ? Get( C,
				this.Symbol.species ) : undefined;
			// b. If C is null, set C to undefined.
			if (C === null) {
				C = undefined;
			}
		}
		// 8. If C is undefined, return ? ArrayCreate(length).
		if (C === undefined) {
			return ArrayCreate( length );
		}
		// 9. If IsConstructor(C) is false, throw a TypeError exception.
		if (!IsConstructor( C )) {
			throw new TypeError( 'C must be a constructor' );
		}
		// 10. Return ? Construct(C, « length »).
		return Construct( C, [ length ] );
	}

	if (!( 'filter' in Array.prototype
	)) {

// Array.prototype.filter
		/* global CreateMethodProperty, ToObject, ToLength, Get, IsCallable, ArraySpeciesCreate, ToString, HasProperty, ToBoolean, Call, CreateDataPropertyOrThrow */
// 22.1.3.7. Array.prototype.filter ( callbackfn [ , thisArg ] )
		CreateMethodProperty( Array.prototype, 'filter',
			function filter( callbackfn /* [ , thisArg ] */ ) {
				// 1. Let O be ? ToObject(this value).
				var O = ToObject( this );
				// 2. Let len be ? ToLength(? Get(O, "length")).
				var len = ToLength( Get( O, 'length' ) );
				// 3. If IsCallable(callbackfn) is false, throw a TypeError
				// exception.
				if (IsCallable( callbackfn ) === false) {
					throw new TypeError( callbackfn + ' is not a function' );
				}
				// 4. If thisArg is present, let T be thisArg; else let T be
				// undefined.
				var T = arguments.length > 1 ? arguments[ 1 ] : undefined;
				// 5. Let A be ? ArraySpeciesCreate(O, 0).
				var A = ArraySpeciesCreate( O, 0 );
				// 6. Let k be 0.
				var k = 0;
				// 7. Let to be 0.
				var to = 0;
				// 8. Repeat, while k < len
				while (k < len) {
					// a. Let Pk be ! ToString(k).
					var Pk = ToString( k );
					// b. Let kPresent be ? HasProperty(O, Pk).
					var kPresent = HasProperty( O, Pk );
					// c. If kPresent is true, then
					if (kPresent) {
						// i. Let kValue be ? Get(O, Pk).
						var kValue = Get( O, Pk );
						// ii. Let selected be ToBoolean(? Call(callbackfn, T,
						// « kValue, k, O »)).
						var selected = ToBoolean( Call( callbackfn, T, [ kValue, k, O ] ) );
						// iii. If selected is true, then
						if (selected) {
							// 1. Perform ? CreateDataPropertyOrThrow(A, !
							// ToString(to), kValue)
							CreateDataPropertyOrThrow( A, ToString( to ), kValue );
							// 2. Increase to by 1.
							to = to + 1;
						}

					}
					// d. Increase k by 1.
					k = k + 1;
				}
				// 9. Return A.
				return A;
			} );

	}

	if (!( 'map' in Array.prototype
	)) {

// Array.prototype.map
		/* global ArraySpeciesCreate, Call, CreateDataPropertyOrThrow, CreateMethodProperty, Get, HasProperty, IsCallable, ToLength, ToObject, ToString */
		/* global CreateMethodProperty, ToObject, ToLength, Get, ArraySpeciesCreate, ToString, HasProperty, Call, CreateDataPropertyOrThrow */
// 22.1.3.16. Array.prototype.map ( callbackfn [ , thisArg ] )
		CreateMethodProperty( Array.prototype, 'map',
			function map( callbackfn /* [ , thisArg ] */ ) {
				// 1. Let O be ? ToObject(this value).
				var O = ToObject( this );
				// 2. Let len be ? ToLength(? Get(O, "length")).
				var len = ToLength( Get( O, 'length' ) );
				// 3. If IsCallable(callbackfn) is false, throw a TypeError
				// exception.
				if (IsCallable( callbackfn ) === false) {
					throw new TypeError( callbackfn + ' is not a function' );
				}
				// 4. If thisArg is present, let T be thisArg; else let T be
				// undefined.
				var T = arguments.length > 1 ? arguments[ 1 ] : undefined;
				// 5. Let A be ? ArraySpeciesCreate(O, len).
				var A = ArraySpeciesCreate( O, len );
				// 6. Let k be 0.
				var k = 0;
				// 7. Repeat, while k < len
				while (k < len) {
					// a. Let Pk be ! ToString(k).
					var Pk = ToString( k );
					// b. Let kPresent be ? HasProperty(O, Pk).
					var kPresent = HasProperty( O, Pk );
					// c. If kPresent is true, then
					if (kPresent) {
						// i. Let kValue be ? Get(O, Pk).
						var kValue = Get( O, Pk );
						// ii. Let mappedValue be ? Call(callbackfn, T, «
						// kValue, k, O »).
						var mappedValue = Call( callbackfn, T, [ kValue, k, O ] );
						// iii. Perform ? CreateDataPropertyOrThrow(A, Pk,
						// mappedValue).
						CreateDataPropertyOrThrow( A, Pk, mappedValue );
					}
					// d. Increase k by 1.
					k = k + 1;
				}
				// 8. Return A.
				return A;
			} );

	}

// Object.setPrototypeOf
	/* global CreateMethodProperty */
// ES6-shim 0.16.0 (c) 2013-2014 Paul Miller (http://paulmillr.com)
// ES6-shim may be freely distributed under the MIT license.
// For more details and documentation:
// https://github.com/paulmillr/es6-shim/

	// NOTE:  This versions needs object ownership
	//        because every promoted object needs to be reassigned
	//        otherwise uncompatible browsers cannot work as expected
	//
	// NOTE:  This might need es5-shim or polyfills upfront
	//        because it's based on ES5 API.
	//        (probably just an IE <= 8 problem)
	//
	// NOTE:  nodejs is fine in version 0.8, 0.10, and future versions.
	( function() {
		if (Object.setPrototypeOf) { return; }

		/*jshint proto: true */
		// @author    Andrea Giammarchi - @WebReflection

		var getOwnPropertyNames = Object.getOwnPropertyNames;
		var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
		var create = Object.create;
		var defineProperty = Object.defineProperty;
		var getPrototypeOf = Object.getPrototypeOf;
		var objProto = Object.prototype;

		var copyDescriptors = function( target, source ) {
			// define into target descriptors from source
			getOwnPropertyNames( source ).forEach( function( key ) {
				defineProperty(
					target,
					key,
					getOwnPropertyDescriptor( source, key ),
				);
			} );
			return target;
		};
		// used as fallback when no promotion is possible
		var createAndCopy = function setPrototypeOf( origin, proto ) {
			return copyDescriptors( create( proto ), origin );
		};
		var set, sPOf;
		try {
			// this might fail for various reasons
			// ignore if Chrome cought it at runtime
			set = getOwnPropertyDescriptor( objProto, '__proto__' ).set;
			set.call( {}, null );
			// setter not poisoned, it can promote
			// Firefox, Chrome
			sPOf = function setPrototypeOf( origin, proto ) {
				set.call( origin, proto );
				return origin;
			};
		}
		catch ( e ) {
			// do one or more feature detections
			set = { __proto__: null };
			// if proto does not work, needs to fallback
			// some Opera, Rhino, ducktape
			if (set instanceof Object) {
				sPOf = createAndCopy;
			}
			else {
				// verify if null objects are buggy
				/* eslint-disable no-proto */
				set.__proto__ = objProto;
				/* eslint-enable no-proto */
				// if null objects are buggy
				// nodejs 0.8 to 0.10
				if (set instanceof Object) {
					sPOf = function setPrototypeOf( origin, proto ) {
						// use such bug to promote
						/* eslint-disable no-proto */
						origin.__proto__ = proto;
						/* eslint-enable no-proto */
						return origin;
					};
				}
				else {
					// try to use proto or fallback
					// Safari, old Firefox, many others
					sPOf = function setPrototypeOf( origin, proto ) {
						// if proto is not null
						if (getPrototypeOf( origin )) {
							// use __proto__ to promote
							/* eslint-disable no-proto */
							origin.__proto__ = proto;
							/* eslint-enable no-proto */
							return origin;
						}
						else {
							// otherwise unable to promote: fallback
							return createAndCopy( origin, proto );
						}
					};
				}
			}
		}
		CreateMethodProperty( Object, 'setPrototypeOf', sPOf );
	}() );
	if (!( 'Promise' in this
	)) {

// Promise
		!function( n ) {
			function t( r ) {
				if (e[ r ]) {
					return e[ r ].exports;
				}
				var o = e[ r ] = { i: r, l: !1, exports: {} };
				return n[ r ].call( o.exports, o, o.exports, t ), o.l = !0, o.exports;
			}

			var e = {};
			t.m = n, t.c = e, t.i = function( n ) {return n;}, t.d = function(
				n, e, r ) {
				t.o( n, e ) || Object.defineProperty( n, e,
					{ configurable: !1, enumerable: !0, get: r } );
			}, t.n = function( n ) {
				var e = n && n.__esModule
					? function() {return n[ 'default' ];}
					: function() {return n;};
				return t.d( e, 'a', e ), e;
			}, t.o = function( n, t ) {
				return Object.prototype.hasOwnProperty.call( n, t );
			}, t.p = '', t( t.s = 100 );
		}( {
			100:/*!***********************!*\
  !*** ./src/global.js ***!
  \***********************/
				function( n, t, e ) {
					( function( n ) {
						var t = e(/*! ./yaku */5 );
						try {n.Promise = t, window.Promise = t;}
						catch ( r ) {}
					} ).call( t, e(/*! ./../~/webpack/buildin/global.js */2 ) );
				}, 2:/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
				function( n, t ) {
					var e;
					e = function() {return this;}();
					try {e = e || Function( 'return this' )() || ( 0, eval )( 'this' );}
					catch ( r ) {'object' == typeof window && ( e = window );}
					n.exports = e;
				}, 5:/*!*********************!*\
  !*** ./src/yaku.js ***!
  \*********************/
				function( n, t, e ) {
					( function( t ) {
						!function() {
							'use strict';

							function e() {return rn[ q ][ B ] || D;}

							function r( n ) {return n && 'object' == typeof n;}

							function o( n ) {return 'function' == typeof n;}

							function i( n, t ) {return n instanceof t;}

							function u( n ) {return i( n, M );}

							function c( n, t, e ) {
								if (!t( n )) {
									throw h( e );
								}
							}

							function f() {
								try {return R.apply( S, arguments );}
								catch ( n ) {return nn.e = n, nn;}
							}

							function s( n, t ) {return R = n, S = t, f;}

							function a( n, t ) {
								function e() {
									for (var e = 0; e < o;) {
										t( r[ e ],
											r[ e + 1 ] ), r[ e ++ ] = P, r[ e ++ ] = P;
									}
									o = 0, r.length > n && ( r.length = n );
								}

								var r = A( n ), o = 0;
								return function( n, t ) {
									r[ o ++ ] = n, r[ o ++ ] = t, 2 === o && rn.nextTick( e );
								};
							}

							function l( n, t ) {
								var e, r, u, c, f = 0;
								if (!n) {
									throw h( Q );
								}
								var a = n[ rn[ q ][ z ] ];
								if (o( a )) {
									r = a.call( n );
								}
								else {
									if (!o( n.next )) {
										if (i( n, A )) {
											for (e = n.length; f < e;) {
												t( n[ f ], f ++ );
											}
											return f;
										}
										throw h( Q );
									}
									r = n;
								}
								for (; !( u = r.next() ).done;) {
									if (( c = s( t )( u.value,
										f ++ ) ) === nn) {
										throw o( r[ G ] ) && r[ G ](), c.e;
									}
								}
								return f;
							}

							function h( n ) {return new TypeError( n );}

							function v( n ) {return ( n ? '' : V ) + ( new M ).stack;}

							function _( n, t ) {
								var e = 'on' + n.toLowerCase(), r = O[ e ];
								H && H.listeners( n ).length ? n === Z
									? H.emit( n, t._v, t )
									: H.emit( n, t ) : r
									? r( { reason: t._v, promise: t } )
									: rn[ n ]( t._v, t );
							}

							function p( n ) {return n && n._s;}

							function d( n ) {
								if (p( n )) {
									return new n( tn );
								}
								var t, e, r;
								return t = new n( function( n, o ) {
									if (t) {
										throw h();
									}
									e = n, r = o;
								} ), c( e, o ), c( r, o ), t;
							}

							function w( n, t ) {
								var e = !1;
								return function( r ) {
									e ||
									( e = !0, L && ( n[ N ] = v( !0 ) ), t === Y ? k( n, r ) : x(
										n, t, r ) );
								};
							}

							function y( n, t, e, r ) {
								return o( e ) && ( t._onFulfilled = e ), o( r ) &&
								( n[ J ] && _( X, n ), t._onRejected = r ), L &&
								( t._p = n ), n[ n._c ++ ] = t, n._s !== $ && on( n, t ), t;
							}

							function m( n ) {
								if (n._umark) {
									return !0;
								}
								n._umark = !0;
								for (var t, e = 0, r = n._c; e <
								r;) {
									if (t = n[ e ++ ], t._onRejected || m( t )) {
										return !0;
									}
								}
							}

							function j( n, t ) {
								function e( n ) {
									return r.push( n.replace( /^\s+|\s+$/g, '' ) );
								}

								var r = [];
								return L && ( t[ N ] && e( t[ N ] ), function o( n ) {
									n && K in n && ( o( n._next ), e( n[ K ] + '' ), o( n._p ) );
								}( t ) ), ( n && n.stack ? n.stack : n ) +
								( '\n' + r.join( '\n' ) ).replace( en, '' );
							}

							function g( n, t ) {return n( t );}

							function x( n, t, e ) {
								var r = 0, o = n._c;
								if (n._s === $) {
									for (n._s = t, n._v = e, t === U &&
									( L && u( e ) && ( e.longStack = j( e, n ) ), un( n ) ); r <
											 o;) {
										on( n, n[ r ++ ] );
									}
								}
								return n;
							}

							function k( n, t ) {
								if (t === n && t) {
									return x( n, U, h( W ) ), n;
								}
								if (t !== C && ( o( t ) || r( t ) )) {
									var e = s( b )( t );
									if (e === nn) {
										return x( n, U, e.e ), n;
									}
									o( e ) ? ( L && p( t ) && ( n._next = t ), p( t ) ? T( n, t,
										e ) : rn.nextTick( function() {T( n, t, e );} ) ) : x( n, Y,
										t );
								}
								else {
									x( n, Y, t );
								}
								return n;
							}

							function b( n ) {return n.then;}

							function T( n, t, e ) {
								var r = s( e, t )( function( e ) {t && ( t = C, k( n, e ) );},
									function( e ) {t && ( t = C, x( n, U, e ) );} );
								r === nn && t && ( x( n, U, r.e ), t = C );
							}

							var P, R, S, C = null, F = 'object' == typeof self,
								O = F ? self : t, E = O.Promise, H = O.process, I = O.console,
								L = !1, A = Array, M = Error, U = 1, Y = 2, $ = 3, q = 'Symbol',
								z = 'iterator', B = 'species', D = q + '(' + B + ')',
								G = 'return', J = '_uh', K = '_pt', N = '_st',
								Q = 'Invalid argument', V = '\nFrom previous ',
								W = 'Chaining cycle detected for promise',
								X = 'rejectionHandled', Z = 'unhandledRejection', nn = { e: C },
								tn = function() {}, en = /^.+\/node_modules\/yaku\/.+\n?/gm,
								rn = function( n ) {
									var t, e = this;
									if (!r( e ) || e._s !== P) {
										throw h( 'Invalid this' );
									}
									if (e._s = $, L && ( e[ K ] = v() ), n !== tn) {
										if (!o( n )) {
											throw h( Q );
										}
										t = s( n )( w( e, Y ), w( e, U ) ), t === nn &&
										x( e, U, t.e );
									}
								};
							rn[ 'default' ] = rn, function(
								n, t ) {
								for (var e in t) {
									n[ e ] = t[ e ];
								}
							}( rn.prototype, {
								then: function( n, t ) {
									if (this._s === undefined) {
										throw h();
									}
									return y( this, d( rn.speciesConstructor( this, rn ) ), n, t );
								},
								'catch': function( n ) {return this.then( P, n );},
								'finally': function( n ) {
									return this.then( function( t ) {
										return rn.resolve( n() ).
											then( function() {return t;} );
									}, function( t ) {
										return rn.resolve( n() ).
											then( function() {throw t;} );
									} );
								},
								_c: 0,
								_p: C,
							} ), rn.resolve = function( n ) {
								return p( n ) ? n : k( d( this ), n );
							}, rn.reject = function( n ) {
								return x( d( this ), U, n );
							}, rn.race = function( n ) {
								var t = this, e = d( t ), r = function( n ) {x( e, Y, n );},
									o = function( n ) {x( e, U, n );},
									i = s( l )( n, function( n ) {t.resolve( n ).then( r, o );} );
								return i === nn ? t.reject( i.e ) : e;
							}, rn.all = function( n ) {
								function t( n ) {x( o, U, n );}

								var e, r = this, o = d( r ), i = [];
								return ( e = s( l )( n, function( n, u ) {
									r.resolve( n ).
										then( function( n ) {i[ u ] = n, -- e || x( o, Y, i );}, t );
								} ) ) === nn ? r.reject( e.e ) : ( e || x( o, Y, [] ), o );
							}, rn.Symbol = O[ q ] || {}, s( function() {
								Object.defineProperty( rn, e(),
									{ get: function() {return this;} } );
							} )(), rn.speciesConstructor = function(
								n, t ) {
								var r = n.constructor;
								return r ? r[ e() ] || t : t;
							}, rn.unhandledRejection = function( n, t ) {
								I &&
								I.error( 'Uncaught (in promise)', L ? t.longStack : j( n, t ) );
							}, rn.rejectionHandled = tn, rn.enableLongStackTrace = function() {L = !0;}, rn.nextTick = F
								? function( n ) {
									E
										? new E( function( n ) {n();} ).then( n )
										: setTimeout( n );
								}
								: H.nextTick, rn._s = 1;
							var on = a( 999, function( n, t ) {
								var e, r;
								return ( r = n._s !== U ? t._onFulfilled : t._onRejected ) === P
									? void x( t, n._s, n._v )
									: ( e = s( g )( r, n._v ) ) === nn
										? void x( t, U, e.e )
										: void k( t, e );
							} ), un = a( 9,
								function( n ) {m( n ) || ( n[ J ] = 1, _( Z, n ) );} );
							try {n.exports = rn;}
							catch ( cn ) {O.Yaku = rn;}
						}();
					} ).call( t, e(/*! ./../~/webpack/buildin/global.js */2 ) );
				},
		} );
	}

	if (!( 'includes' in String.prototype
	)) {

// String.prototype.includes
		/* global CreateMethodProperty, IsRegExp, RequireObjectCoercible, ToInteger, ToString */
// 21.1.3.7. String.prototype.includes ( searchString [ , position ] )
		CreateMethodProperty( String.prototype, 'includes',
			function includes( searchString /* [ , position ] */ ) {
				'use strict';
				var position = arguments.length > 1 ? arguments[ 1 ] : undefined;
				// 1. Let O be ? RequireObjectCoercible(this value).
				var O = RequireObjectCoercible( this );
				// 2. Let S be ? ToString(O).
				var S = ToString( O );
				// 3. Let isRegExp be ? IsRegExp(searchString).
				var isRegExp = IsRegExp( searchString );
				// 4. If isRegExp is true, throw a TypeError exception.
				if (isRegExp) {
					throw new TypeError(
						'First argument to String.prototype.includes must not be a regular expression' );
				}
				// 5. Let searchStr be ? ToString(searchString).
				var searchStr = ToString( searchString );
				// 6. Let pos be ? ToInteger(position). (If position is
				// undefined, this step produces the value 0.)
				var pos = ToInteger( position );
				// 7. Let len be the length of S.
				var len = S.length;
				// 8. Let start be min(max(pos, 0), len).
				var start = Math.min( Math.max( pos, 0 ), len );
				// 9. Let searchLen be the length of searchStr.
				// var searchLength = searchStr.length;
				// 10. If there exists any integer k not smaller than start
				// such that k + searchLen is not greater than len, and for all
				// nonnegative integers j less than searchLen, the code unit at
				// index k+j within S is the same as the code unit at index j
				// within searchStr, return true; but if there is no such
				// integer k, return false.
				return String.prototype.indexOf.call( S, searchStr, start ) !== - 1;
			} );

	}

	if (!( 'Symbol' in this && 0 === this.Symbol.length
	)) {

// Symbol
// A modification of https://github.com/WebReflection/get-own-property-symbols
// (C) Andrea Giammarchi - MIT Licensed

		( function( Object, GOPS, global ) {

			var setDescriptor;
			var id = 0;
			var random = '' + Math.random();
			var prefix = '__\x01symbol:';
			var prefixLength = prefix.length;
			var internalSymbol = '__\x01symbol@@' + random;
			var DP = 'defineProperty';
			var DPies = 'defineProperties';
			var GOPN = 'getOwnPropertyNames';
			var GOPD = 'getOwnPropertyDescriptor';
			var PIE = 'propertyIsEnumerable';
			var ObjectProto = Object.prototype;
			var hOP = ObjectProto.hasOwnProperty;
			var pIE = ObjectProto[ PIE ];
			var toString = ObjectProto.toString;
			var concat = Array.prototype.concat;
			var cachedWindowNames = typeof window === 'object'
				? Object.getOwnPropertyNames( window )
				: [];
			var nGOPN = Object[ GOPN ];
			var gOPN = function getOwnPropertyNames( obj ) {
				if (toString.call( obj ) === '[object Window]') {
					try {
						return nGOPN( obj );
					}
					catch ( e ) {
						// IE bug where layout engine calls userland gOPN for
						// cross-domain `window` objects
						return concat.call( [], cachedWindowNames );
					}
				}
				return nGOPN( obj );
			};
			var gOPD = Object[ GOPD ];
			var create = Object.create;
			var keys = Object.keys;
			var freeze = Object.freeze || Object;
			var defineProperty = Object[ DP ];
			var $defineProperties = Object[ DPies ];
			var descriptor = gOPD( Object, GOPN );
			var addInternalIfNeeded = function( o, uid, enumerable ) {
				if (!hOP.call( o, internalSymbol )) {
					try {
						defineProperty( o, internalSymbol, {
							enumerable: false,
							configurable: false,
							writable: false,
							value: {},
						} );
					}
					catch ( e ) {
						o[ internalSymbol ] = {};
					}
				}
				o[ internalSymbol ][ '@@' + uid ] = enumerable;
			};
			var createWithSymbols = function( proto, descriptors ) {
				var self = create( proto );
				gOPN( descriptors ).forEach( function( key ) {
					if (propertyIsEnumerable.call( descriptors, key )) {
						$defineProperty( self, key, descriptors[ key ] );
					}
				} );
				return self;
			};
			var copyAsNonEnumerable = function( descriptor ) {
				var newDescriptor = create( descriptor );
				newDescriptor.enumerable = false;
				return newDescriptor;
			};
			var get = function get() {};
			var onlyNonSymbols = function( name ) {
				return name != internalSymbol &&
					!hOP.call( source, name );
			};
			var onlySymbols = function( name ) {
				return name != internalSymbol &&
					hOP.call( source, name );
			};
			var propertyIsEnumerable = function propertyIsEnumerable( key ) {
				var uid = '' + key;
				return onlySymbols( uid ) ? (
					hOP.call( this, uid ) &&
					this[ internalSymbol ][ '@@' + uid ]
				) : pIE.call( this, key );
			};
			var setAndGetSymbol = function( uid ) {
				var descriptor = {
					enumerable: false,
					configurable: true,
					get: get,
					set: function( value ) {
						setDescriptor( this, uid, {
							enumerable: false,
							configurable: true,
							writable: true,
							value: value,
						} );
						addInternalIfNeeded( this, uid, true );
					},
				};
				try {
					defineProperty( ObjectProto, uid, descriptor );
				}
				catch ( e ) {
					ObjectProto[ uid ] = descriptor.value;
				}
				return freeze( source[ uid ] = defineProperty(
					Object( uid ),
					'constructor',
					sourceConstructor,
				) );
			};
			var Symbol = function Symbol() {
				var description = arguments[ 0 ];
				if (this instanceof Symbol) {
					throw new TypeError( 'Symbol is not a constructor' );
				}
				return setAndGetSymbol(
					prefix.concat( description || '', random, ++ id ),
				);
			};
			var source = create( null );
			var sourceConstructor = { value: Symbol };
			var sourceMap = function( uid ) {
				return source[ uid ];
			};
			var $defineProperty = function defineProp( o, key, descriptor ) {
				var uid = '' + key;
				if (onlySymbols( uid )) {
					setDescriptor( o, uid, descriptor.enumerable ?
						copyAsNonEnumerable( descriptor ) : descriptor );
					addInternalIfNeeded( o, uid, !!descriptor.enumerable );
				}
				else {
					defineProperty( o, key, descriptor );
				}
				return o;
			};

			var onlyInternalSymbols = function( obj ) {
				return function( name ) {
					return hOP.call( obj, internalSymbol ) &&
						hOP.call( obj[ internalSymbol ], '@@' + name );
				};
			};
			var $getOwnPropertySymbols = function getOwnPropertySymbols( o ) {
					return gOPN( o ).
						filter( o === ObjectProto ? onlyInternalSymbols( o ) : onlySymbols ).
						map( sourceMap );
				}
			;

			descriptor.value = $defineProperty;
			defineProperty( Object, DP, descriptor );

			descriptor.value = $getOwnPropertySymbols;
			defineProperty( Object, GOPS, descriptor );

			descriptor.value = function getOwnPropertyNames( o ) {
				return gOPN( o ).filter( onlyNonSymbols );
			};
			defineProperty( Object, GOPN, descriptor );

			descriptor.value = function defineProperties( o, descriptors ) {
				var symbols = $getOwnPropertySymbols( descriptors );
				if (symbols.length) {
					keys( descriptors ).concat( symbols ).forEach( function( uid ) {
						if (propertyIsEnumerable.call( descriptors, uid )) {
							$defineProperty( o, uid, descriptors[ uid ] );
						}
					} );
				}
				else {
					$defineProperties( o, descriptors );
				}
				return o;
			};
			defineProperty( Object, DPies, descriptor );

			descriptor.value = propertyIsEnumerable;
			defineProperty( ObjectProto, PIE, descriptor );

			descriptor.value = Symbol;
			defineProperty( global, 'Symbol', descriptor );

			// defining `Symbol.for(key)`
			descriptor.value = function( key ) {
				var uid = prefix.concat( prefix, key, random );
				return uid in ObjectProto ? source[ uid ] : setAndGetSymbol( uid );
			};
			defineProperty( Symbol, 'for', descriptor );

			// defining `Symbol.keyFor(symbol)`
			descriptor.value = function( symbol ) {
				if (onlyNonSymbols( symbol )) {
					throw new TypeError( symbol + ' is not a symbol' );
				}
				return hOP.call( source, symbol ) ?
					symbol.slice( prefixLength * 2, - random.length ) :
					void 0
					;
			};
			defineProperty( Symbol, 'keyFor', descriptor );

			descriptor.value = function getOwnPropertyDescriptor( o, key ) {
				var descriptor = gOPD( o, key );
				if (descriptor && onlySymbols( key )) {
					descriptor.enumerable = propertyIsEnumerable.call( o, key );
				}
				return descriptor;
			};
			defineProperty( Object, GOPD, descriptor );

			descriptor.value = function( proto, descriptors ) {
				return arguments.length === 1 || typeof descriptors === 'undefined' ?
					create( proto ) :
					createWithSymbols( proto, descriptors );
			};
			defineProperty( Object, 'create', descriptor );

			descriptor.value = function() {
				var str = toString.call( this );
				return ( str === '[object String]' && onlySymbols( this ) )
					? '[object Symbol]'
					: str;
			};
			defineProperty( ObjectProto, 'toString', descriptor );

			setDescriptor = function( o, key, descriptor ) {
				var protoDescriptor = gOPD( ObjectProto, key );
				delete ObjectProto[ key ];
				defineProperty( o, key, descriptor );
				if (o !== ObjectProto) {
					defineProperty( ObjectProto, key, protoDescriptor );
				}
			};

		}( Object, 'getOwnPropertySymbols', this ) );

	}

	if (!( 'Symbol' in this && 'iterator' in this.Symbol
	)) {

// Symbol.iterator
		/* global Symbol */
		Object.defineProperty( Symbol, 'iterator',
			{ value: Symbol( 'iterator' ) } );

	}

	if (!( 'Symbol' in this && 'toStringTag' in this.Symbol
	)) {

// Symbol.toStringTag
		/* global Symbol */
		Object.defineProperty( Symbol, 'toStringTag', {
			value: Symbol( 'toStringTag' ),
		} );

	}

// _Iterator
	/* global Symbol */
// A modification of https://github.com/medikoo/es6-iterator
// Copyright (C) 2013-2015 Mariusz Nowak (www.medikoo.com)

	var Iterator = ( function() { // eslint-disable-line no-unused-vars
		var clear = function() {
			this.length = 0;
			return this;
		};
		var callable = function( fn ) {
			if (typeof fn !== 'function') {
				throw new TypeError(
					fn + ' is not a function' );
			}
			return fn;
		};

		var Iterator = function( list, context ) {
			if (!( this instanceof Iterator )) {
				return new Iterator( list, context );
			}
			Object.defineProperties( this, {
				__list__: {
					writable: true,
					value: list,
				},
				__context__: {
					writable: true,
					value: context,
				},
				__nextIndex__: {
					writable: true,
					value: 0,
				},
			} );
			if (!context) {
				return;
			}
			callable( context.on );
			context.on( '_add', this._onAdd.bind( this ) );
			context.on( '_delete', this._onDelete.bind( this ) );
			context.on( '_clear', this._onClear.bind( this ) );
		};

		Object.defineProperties( Iterator.prototype, Object.assign( {
			constructor: {
				value: Iterator,
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_next: {
				value: function() {
					var i;
					if (!this.__list__) {
						return;
					}
					if (this.__redo__) {
						i = this.__redo__.shift();
						if (i !== undefined) {
							return i;
						}
					}
					if (this.__nextIndex__ <
						this.__list__.length) {
						return this.__nextIndex__ ++;
					}
					this._unBind();
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			next: {
				value: function() {
					return this._createResult( this._next() );
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_createResult: {
				value: function( i ) {
					if (i === undefined) {
						return {
							done: true,
							value: undefined,
						};
					}
					return {
						done: false,
						value: this._resolve( i ),
					};
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_resolve: {
				value: function( i ) {
					return this.__list__[ i ];
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_unBind: {
				value: function() {
					this.__list__ = null;
					delete this.__redo__;
					if (!this.__context__) {
						return;
					}
					this.__context__.off( '_add', this._onAdd.bind( this ) );
					this.__context__.off( '_delete', this._onDelete.bind( this ) );
					this.__context__.off( '_clear', this._onClear.bind( this ) );
					this.__context__ = null;
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			toString: {
				value: function() {
					return '[object Iterator]';
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
		}, {
			_onAdd: {
				value: function( index ) {
					if (index >= this.__nextIndex__) {
						return;
					}
					++ this.__nextIndex__;
					if (!this.__redo__) {
						Object.defineProperty( this, '__redo__', {
							value: [ index ],
							configurable: true,
							enumerable: false,
							writable: false,
						} );
						return;
					}
					this.__redo__.forEach( function( redo, i ) {
						if (redo >= index) {
							this.__redo__[ i ] = ++ redo;
						}
					}, this );
					this.__redo__.push( index );
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_onDelete: {
				value: function( index ) {
					var i;
					if (index >= this.__nextIndex__) {
						return;
					}
					-- this.__nextIndex__;
					if (!this.__redo__) {
						return;
					}
					i = this.__redo__.indexOf( index );
					if (i !== - 1) {
						this.__redo__.splice( i, 1 );
					}
					this.__redo__.forEach( function( redo, i ) {
						if (redo > index) {
							this.__redo__[ i ] = -- redo;
						}
					}, this );
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_onClear: {
				value: function() {
					if (this.__redo__) {
						clear.call( this.__redo__ );
					}
					this.__nextIndex__ = 0;
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
		} ) );

		Object.defineProperty( Iterator.prototype, Symbol.iterator, {
			value: function() {
				return this;
			},
			configurable: true,
			enumerable: false,
			writable: true,
		} );
		Object.defineProperty( Iterator.prototype, Symbol.toStringTag, {
			value: 'Iterator',
			configurable: false,
			enumerable: false,
			writable: true,
		} );

		return Iterator;
	}() );

// _ArrayIterator
	/* global Iterator */
// A modification of https://github.com/medikoo/es6-iterator
// Copyright (C) 2013-2015 Mariusz Nowak (www.medikoo.com)

	var ArrayIterator = ( function() { // eslint-disable-line no-unused-vars

		var ArrayIterator = function( arr, kind ) {
			if (!( this instanceof ArrayIterator )) {
				return new ArrayIterator( arr,
					kind );
			}
			Iterator.call( this, arr );
			if (!kind) {
				kind = 'value';
			}
			else if (String.prototype.includes.call( kind,
				'key+value' )) {
				kind = 'key+value';
			}
			else if (String.prototype.includes.call( kind, 'key' )) {
				kind = 'key';
			}
			else {
				kind = 'value';
			}
			Object.defineProperty( this, '__kind__', {
				value: kind,
				configurable: false,
				enumerable: false,
				writable: false,
			} );
		};
		if (Object.setPrototypeOf) {
			Object.setPrototypeOf( ArrayIterator,
				Iterator.prototype );
		}

		ArrayIterator.prototype = Object.create( Iterator.prototype, {
			constructor: {
				value: ArrayIterator,
				configurable: true,
				enumerable: false,
				writable: true,
			},
			_resolve: {
				value: function( i ) {
					if (this.__kind__ === 'value') {
						return this.__list__[ i ];
					}
					if (this.__kind__ === 'key+value') {
						return [ i, this.__list__[ i ] ];
					}
					return i;
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
			toString: {
				value: function() {
					return '[object Array Iterator]';
				},
				configurable: true,
				enumerable: false,
				writable: true,
			},
		} );

		return ArrayIterator;
	}() );
	if (!( 'Symbol' in this && 'iterator' in this.Symbol &&
		!!Array.prototype.entries
	)) {

// Array.prototype.entries
		/* global CreateMethodProperty, ToObject */
// 22.1.3.4. Array.prototype.entries ( )
		CreateMethodProperty( Array.prototype, 'entries', function entries() {
			// 1. Let O be ? ToObject(this value).
			var O = ToObject( this );
			// 2. Return CreateArrayIterator(O, "key+value").
			// TODO: Add CreateArrayIterator
			return new ArrayIterator( O, 'key+value' );
		} );

	}

	if (!( ( function( e ) {
			'use strict';
			try {
				var a = new e.URL( 'http://example.com' );
				if ('href' in a && 'searchParams' in a) {
					var r = new URL( 'http://example.com' );
					if (r.search = 'a=1&b=2', 'http://example.com/?a=1&b=2' === r.href &&
					( r.search = '', 'http://example.com/' === r.href )) {
						var t = new e.URLSearchParams( 'a=1' ),
							h = new e.URLSearchParams( t );
						if ('a=1' === String( h )) {
							return !0;
						}
					}
				}
				return !1;
			}
			catch ( c ) {return !1;}
		} )( this )
	)) {

// URL
		/* global Symbol */
// URL Polyfill
// Draft specification: https://url.spec.whatwg.org

// Notes:
// - Primarily useful for parsing URLs and modifying query parameters
// - Should work in IE8+ and everything more modern, with es5.js polyfills

		( function( global ) {
			'use strict';

			function isSequence( o ) {
				if (!o) {
					return false;
				}
				if ('Symbol' in global && 'iterator' in global.Symbol &&
					typeof o[ Symbol.iterator ] === 'function') {
					return true;
				}
				if (Array.isArray( o )) {
					return true;
				}
				return false;
			}

			function toArray( iter ) {
				return ( 'from' in Array )
					? Array.from( iter )
					: Array.prototype.slice.call( iter );
			}

			( function() {

				// Browsers may have:
				// * No global URL object
				// * URL with static methods only - may have a dummy constructor
				// * URL with members except searchParams
				// * Full URL API support
				var origURL = global.URL;
				var nativeURL;
				try {
					if (origURL) {
						nativeURL = new global.URL( 'http://example.com' );
						if ('searchParams' in nativeURL) {
							var url = new URL( 'http://example.com' );
							url.search = 'a=1&b=2';
							if (url.href === 'http://example.com/?a=1&b=2') {
								url.search = '';
								if (url.href === 'http://example.com/') {
									return;
								}
							}
						}
						if (!( 'href' in nativeURL )) {
							nativeURL = undefined;
						}
						nativeURL = undefined;
					}
				}
				catch ( _ ) {}

				// NOTE: Doesn't do the encoding/decoding dance
				function urlencoded_serialize( pairs ) {
					var output = '', first = true;
					pairs.forEach( function( pair ) {
						var name = encodeURIComponent( pair.name );
						var value = encodeURIComponent( pair.value );
						if (!first) {
							output += '&';
						}
						output += name + '=' + value;
						first = false;
					} );
					return output.replace( /%20/g, '+' );
				}

				// NOTE: Doesn't do the encoding/decoding dance
				function urlencoded_parse( input, isindex ) {
					var sequences = input.split( '&' );
					if (isindex && sequences[ 0 ].indexOf( '=' ) === - 1) {
						sequences[ 0 ] = '=' + sequences[ 0 ];
					}
					var pairs = [];
					sequences.forEach( function( bytes ) {
						if (bytes.length === 0) {
							return;
						}
						var index = bytes.indexOf( '=' );
						if (index !== - 1) {
							var name = bytes.substring( 0, index );
							var value = bytes.substring( index + 1 );
						}
						else {
							name = bytes;
							value = '';
						}
						name = name.replace( /\+/g, ' ' );
						value = value.replace( /\+/g, ' ' );
						pairs.push( { name: name, value: value } );
					} );
					var output = [];
					pairs.forEach( function( pair ) {
						output.push( {
							name: decodeURIComponent( pair.name ),
							value: decodeURIComponent( pair.value ),
						} );
					} );
					return output;
				}

				function URLUtils( url ) {
					if (nativeURL) {
						return new origURL( url );
					}
					var anchor = document.createElement( 'a' );
					anchor.href = url;
					return anchor;
				}

				function URLSearchParams( init ) {
					var $this = this;
					this._list = [];

					if (init === undefined || init === null) {
						// no-op
					}
					else if (init instanceof URLSearchParams) {
						// In ES6 init would be a sequence, but special case
						// for ES5.
						this._list = urlencoded_parse( String( init ) );
					}
					else if (typeof init === 'object' && isSequence( init )) {
						toArray( init ).forEach( function( e ) {
							if (!isSequence( e )) {
								throw TypeError();
							}
							var nv = toArray( e );
							if (nv.length !== 2) {
								throw TypeError();
							}
							$this._list.push(
								{ name: String( nv[ 0 ] ), value: String( nv[ 1 ] ) } );
						} );
					}
					else if (typeof init === 'object' && init) {
						Object.keys( init ).forEach( function( key ) {
							$this._list.push(
								{ name: String( key ), value: String( init[ key ] ) } );
						} );
					}
					else {
						init = String( init );
						if (init.substring( 0, 1 ) === '?') {
							init = init.substring( 1 );
						}
						this._list = urlencoded_parse( init );
					}

					this._url_object = null;
					this._setList = function( list ) {
						if (!updating) {
							$this._list = list;
						}
					};

					var updating = false;
					this._update_steps = function() {
						if (updating) {
							return;
						}
						updating = true;

						if (!$this._url_object) {
							return;
						}

						// Partial workaround for IE issue with 'about:'
						if ($this._url_object.protocol === 'about:' &&
							$this._url_object.pathname.indexOf( '?' ) !== - 1) {
							$this._url_object.pathname = $this._url_object.pathname.split(
								'?' )[ 0 ];
						}

						$this._url_object.search = urlencoded_serialize( $this._list );

						updating = false;
					};
				}

				Object.defineProperties( URLSearchParams.prototype, {
					append: {
						value: function( name, value ) {
							this._list.push( { name: name, value: value } );
							this._update_steps();
						}, writable: true, enumerable: true, configurable: true,
					},

					'delete': {
						value: function( name ) {
							for (var i = 0; i < this._list.length;) {
								if (this._list[ i ].name === name) {
									this._list.splice( i, 1 );
								}
								else {
									++ i;
								}
							}
							this._update_steps();
						}, writable: true, enumerable: true, configurable: true,
					},

					get: {
						value: function( name ) {
							for (var i = 0; i < this._list.length; ++ i) {
								if (this._list[ i ].name === name) {
									return this._list[ i ].value;
								}
							}
							return null;
						}, writable: true, enumerable: true, configurable: true,
					},

					getAll: {
						value: function( name ) {
							var result = [];
							for (var i = 0; i < this._list.length; ++ i) {
								if (this._list[ i ].name === name) {
									result.push( this._list[ i ].value );
								}
							}
							return result;
						}, writable: true, enumerable: true, configurable: true,
					},

					has: {
						value: function( name ) {
							for (var i = 0; i < this._list.length; ++ i) {
								if (this._list[ i ].name === name) {
									return true;
								}
							}
							return false;
						}, writable: true, enumerable: true, configurable: true,
					},

					set: {
						value: function( name, value ) {
							var found = false;
							for (var i = 0; i < this._list.length;) {
								if (this._list[ i ].name === name) {
									if (!found) {
										this._list[ i ].value = value;
										found = true;
										++ i;
									}
									else {
										this._list.splice( i, 1 );
									}
								}
								else {
									++ i;
								}
							}

							if (!found) {
								this._list.push( { name: name, value: value } );
							}

							this._update_steps();
						}, writable: true, enumerable: true, configurable: true,
					},

					entries: {
						value: function() {
							return new Iterator( this._list, 'key+value' );
						},
						writable: true, enumerable: true, configurable: true,
					},

					keys: {
						value: function() { return new Iterator( this._list, 'key' ); },
						writable: true, enumerable: true, configurable: true,
					},

					values: {
						value: function() { return new Iterator( this._list, 'value' ); },
						writable: true, enumerable: true, configurable: true,
					},

					forEach: {
						value: function( callback ) {
							var thisArg = ( arguments.length > 1 )
								? arguments[ 1 ]
								: undefined;
							this._list.forEach( function( pair ) {
								callback.call( thisArg, pair.value, pair.name );
							} );

						}, writable: true, enumerable: true, configurable: true,
					},

					toString: {
						value: function() {
							return urlencoded_serialize( this._list );
						}, writable: true, enumerable: false, configurable: true,
					},
				} );

				function Iterator( source, kind ) {
					var index = 0;
					this[ 'next' ] = function() {
						if (index >= source.length) {
							return { done: true, value: undefined };
						}
						var pair = source[ index ++ ];
						return {
							done: false, value:
								kind === 'key' ? pair.name :
									kind === 'value' ? pair.value :
										[ pair.name, pair.value ],
						};
					};
				}

				if ('Symbol' in global && 'iterator' in global.Symbol) {
					Object.defineProperty( URLSearchParams.prototype,
						global.Symbol.iterator, {
							value: URLSearchParams.prototype.entries,
							writable: true, enumerable: true, configurable: true,
						} );
					Object.defineProperty( Iterator.prototype, global.Symbol.iterator, {
						value: function() { return this; },
						writable: true, enumerable: true, configurable: true,
					} );
				}

				function URL( url, base ) {
					if (!( this instanceof global.URL )) {
						throw new TypeError(
							'Failed to construct \'URL\': Please use the \'new\' operator.' );
					}

					if (base) {
						url = ( function() {
							if (nativeURL) {
								return new origURL( url, base ).href;
							}
							var iframe;
							try {
								var doc;
								// Use another document/base tag/anchor for
								// relative URL resolution, if possible
								if (Object.prototype.toString.call( window.operamini ) ===
									'[object OperaMini]') {
									iframe = document.createElement( 'iframe' );
									iframe.style.display = 'none';
									document.documentElement.appendChild( iframe );
									doc = iframe.contentWindow.document;
								}
								else if (document.implementation &&
									document.implementation.createHTMLDocument) {
									doc = document.implementation.createHTMLDocument( '' );
								}
								else if (document.implementation &&
									document.implementation.createDocument) {
									doc = document.implementation.createDocument(
										'http://www.w3.org/1999/xhtml', 'html', null );
									doc.documentElement.appendChild(
										doc.createElement( 'head' ) );
									doc.documentElement.appendChild(
										doc.createElement( 'body' ) );
								}
								else if (window.ActiveXObject) {
									doc = new window.ActiveXObject( 'htmlfile' );
									doc.write( '<head><\/head><body><\/body>' );
									doc.close();
								}

								if (!doc) {
									throw Error( 'base not supported' );
								}

								var baseTag = doc.createElement( 'base' );
								baseTag.href = base;
								doc.getElementsByTagName( 'head' )[ 0 ].appendChild( baseTag );
								var anchor = doc.createElement( 'a' );
								anchor.href = url;
								return anchor.href;
							}
							finally {
								if (iframe) {
									iframe.parentNode.removeChild( iframe );
								}
							}
						}() );
					}

					// An inner object implementing URLUtils (either a native
					// URL object or an HTMLAnchorElement instance) is used to
					// perform the URL algorithms. With full ES5 getter/setter
					// support, return a regular object For IE8's limited
					// getter/setter support, a different HTMLAnchorElement is
					// returned with properties overridden

					var instance = URLUtils( url || '' );

					// Detect for ES5 getter/setter support
					// (an Object.defineProperties polyfill that doesn't
					// support getters/setters may throw)
					var ES5_GET_SET = ( function() {
						if (!( 'defineProperties' in Object )) {
							return false;
						}
						try {
							var obj = {};
							Object.defineProperties( obj,
								{ prop: { 'get': function() { return true; } } } );
							return obj.prop;
						}
						catch ( _ ) {
							return false;
						}
					}() );

					var self = ES5_GET_SET ? this : document.createElement( 'a' );

					var query_object = new URLSearchParams(
						instance.search ? instance.search.substring( 1 ) : null );
					query_object._url_object = self;

					Object.defineProperties( self, {
						href: {
							get: function() { return instance.href; },
							set: function( v ) {
								instance.href = v;
								tidy_instance();
								update_steps();
							},
							enumerable: true, configurable: true,
						},
						origin: {
							get: function() {
								if ('origin' in instance) {
									return instance.origin;
								}
								return this.protocol + '//' + this.host;
							},
							enumerable: true, configurable: true,
						},
						protocol: {
							get: function() { return instance.protocol; },
							set: function( v ) { instance.protocol = v; },
							enumerable: true, configurable: true,
						},
						username: {
							get: function() { return instance.username; },
							set: function( v ) { instance.username = v; },
							enumerable: true, configurable: true,
						},
						password: {
							get: function() { return instance.password; },
							set: function( v ) { instance.password = v; },
							enumerable: true, configurable: true,
						},
						host: {
							get: function() {
								// IE returns default port in |host|
								var re = {
									'http:': /:80$/,
									'https:': /:443$/,
									'ftp:': /:21$/,
								}[ instance.protocol ];
								return re ? instance.host.replace( re, '' ) : instance.host;
							},
							set: function( v ) { instance.host = v; },
							enumerable: true, configurable: true,
						},
						hostname: {
							get: function() { return instance.hostname; },
							set: function( v ) { instance.hostname = v; },
							enumerable: true, configurable: true,
						},
						port: {
							get: function() { return instance.port; },
							set: function( v ) { instance.port = v; },
							enumerable: true, configurable: true,
						},
						pathname: {
							get: function() {
								// IE does not include leading '/' in |pathname|
								if (instance.pathname.charAt( 0 ) !== '/') {
									return '/' +
										instance.pathname;
								}
								return instance.pathname;
							},
							set: function( v ) { instance.pathname = v; },
							enumerable: true, configurable: true,
						},
						search: {
							get: function() { return instance.search; },
							set: function( v ) {
								if (instance.search === v) {
									return;
								}
								instance.search = v;
								tidy_instance();
								update_steps();
							},
							enumerable: true, configurable: true,
						},
						searchParams: {
							get: function() { return query_object; },
							enumerable: true, configurable: true,
						},
						hash: {
							get: function() { return instance.hash; },
							set: function( v ) {
								instance.hash = v;
								tidy_instance();
							},
							enumerable: true, configurable: true,
						},
						toString: {
							value: function() { return instance.toString(); },
							enumerable: false, configurable: true,
						},
						valueOf: {
							value: function() { return instance.valueOf(); },
							enumerable: false, configurable: true,
						},
					} );

					function tidy_instance() {
						var href = instance.href.replace( /#$|\?$|\?(?=#)/g, '' );
						if (instance.href !== href) {
							instance.href = href;
						}
					}

					function update_steps() {
						query_object._setList( instance.search ? urlencoded_parse(
							instance.search.substring( 1 ) ) : [] );
						query_object._update_steps();
					}

					return self;
				}

				if (origURL) {
					for (var i in origURL) {
						if (origURL.hasOwnProperty( i ) && typeof origURL[ i ] ===
							'function') {
							URL[ i ] = origURL[ i ];
						}
					}
				}

				global.URL = URL;
				global.URLSearchParams = URLSearchParams;
			}() );

			// Patch native URLSearchParams constructor to handle
			// sequences/records if necessary.
			( function() {
				if (new global.URLSearchParams( [ [ 'a', 1 ] ] ).get( 'a' ) === '1' &&
					new global.URLSearchParams( { a: 1 } ).get( 'a' ) === '1') {
					return;
				}
				var orig = global.URLSearchParams;
				global.URLSearchParams = function( init ) {
					if (init && typeof init === 'object' && isSequence( init )) {
						var o = new orig();
						toArray( init ).forEach( function( e ) {
							if (!isSequence( e )) {
								throw TypeError();
							}
							var nv = toArray( e );
							if (nv.length !== 2) {
								throw TypeError();
							}
							o.append( nv[ 0 ], nv[ 1 ] );
						} );
						return o;
					}
					else if (init && typeof init === 'object') {
						o = new orig();
						Object.keys( init ).forEach( function( key ) {
							o.set( key, init[ key ] );
						} );
						return o;
					}
					else {
						return new orig( init );
					}
				};
			}() );

		}( self ) );

	}

	if (!( 'Window' in this
	)) {

// Window
		if (( typeof WorkerGlobalScope === 'undefined' ) &&
			( typeof importScripts !== 'function' )) {
			( function( global ) {
				if (global.constructor) {
					global.Window = global.constructor;
				}
				else {
					( global.Window = global.constructor = new Function(
						'return function Window() {}' )() ).prototype = this;
				}
			}( this ) );
		}

	}

	if (!( ( function( n ) {
			if (!( 'Event' in n )) {
				return !1;
			}
			try {return new Event( 'click' ), !0;}
			catch ( t ) {return !1;}
		} )( this )
	)) {

// Event
		( function() {
			var unlistenableWindowEvents = {
				click: 1,
				dblclick: 1,
				keyup: 1,
				keypress: 1,
				keydown: 1,
				mousedown: 1,
				mouseup: 1,
				mousemove: 1,
				mouseover: 1,
				mouseenter: 1,
				mouseleave: 1,
				mouseout: 1,
				storage: 1,
				storagecommit: 1,
				textinput: 1,
			};

			// This polyfill depends on availability of `document` so will not
			// run in a worker However, we asssume there are no browsers with
			// worker support that lack proper support for `Event` within the
			// worker
			if (typeof document === 'undefined' || typeof window ===
				'undefined') {
				return;
			}

			function indexOf( array, element ) {
				var
					index = - 1,
					length = array.length;

				while (++ index < length) {
					if (index in array && array[ index ] === element) {
						return index;
					}
				}

				return - 1;
			}

			var existingProto = ( window.Event && window.Event.prototype ) || null;

			function Event( type, eventInitDict ) {
				if (!type) {
					throw new Error( 'Not enough arguments' );
				}

				var event;
				// Shortcut if browser supports createEvent
				if ('createEvent' in document) {
					event = document.createEvent( 'Event' );
					var bubbles = eventInitDict && eventInitDict.bubbles !== undefined
						? eventInitDict.bubbles
						: false;
					var cancelable = eventInitDict && eventInitDict.cancelable !==
					undefined ? eventInitDict.cancelable : false;

					event.initEvent( type, bubbles, cancelable );

					return event;
				}

				event = document.createEventObject();

				event.type = type;
				event.bubbles = eventInitDict && eventInitDict.bubbles !== undefined
					? eventInitDict.bubbles
					: false;
				event.cancelable = eventInitDict && eventInitDict.cancelable !==
				undefined ? eventInitDict.cancelable : false;

				return event;
			}

			Event.NONE = 0;
			Event.CAPTURING_PHASE = 1;
			Event.AT_TARGET = 2;
			Event.BUBBLING_PHASE = 3;
			window.Event = Window.prototype.Event = Event;
			if (existingProto) {
				Object.defineProperty( window.Event, 'prototype', {
					configurable: false,
					enumerable: false,
					writable: true,
					value: existingProto,
				} );
			}

			if (!( 'createEvent' in document )) {
				window.addEventListener = Window.prototype.addEventListener = Document.prototype.addEventListener = Element.prototype.addEventListener = function addEventListener() {
					var
						element = this,
						type = arguments[ 0 ],
						listener = arguments[ 1 ];

					if (element === window && type in unlistenableWindowEvents) {
						throw new Error( 'In IE8 the event: ' + type +
							' is not available on the window object. Please see https://github.com/Financial-Times/polyfill-service/issues/317 for more information.' );
					}

					if (!element._events) {
						element._events = {};
					}

					if (!element._events[ type ]) {
						element._events[ type ] = function( event ) {
							var
								list = element._events[ event.type ].list,
								events = list.slice(),
								index = - 1,
								length = events.length,
								eventElement;

							event.preventDefault = function preventDefault() {
								if (event.cancelable !== false) {
									event.returnValue = false;
								}
							};

							event.stopPropagation = function stopPropagation() {
								event.cancelBubble = true;
							};

							event.stopImmediatePropagation = function stopImmediatePropagation() {
								event.cancelBubble = true;
								event.cancelImmediate = true;
							};

							event.currentTarget = element;
							event.relatedTarget = event.fromElement || null;
							event.target = event.target || event.srcElement || element;
							event.timeStamp = new Date().getTime();

							if (event.clientX) {
								event.pageX = event.clientX +
									document.documentElement.scrollLeft;
								event.pageY = event.clientY +
									document.documentElement.scrollTop;
							}

							while (++ index < length && !event.cancelImmediate) {
								if (index in events) {
									eventElement = events[ index ];

									if (indexOf( list, eventElement ) !== - 1 &&
										typeof eventElement === 'function') {
										eventElement.call( element, event );
									}
								}
							}
						};

						element._events[ type ].list = [];

						if (element.attachEvent) {
							element.attachEvent( 'on' + type, element._events[ type ] );
						}
					}

					element._events[ type ].list.push( listener );
				};

				window.removeEventListener = Window.prototype.removeEventListener = Document.prototype.removeEventListener = Element.prototype.removeEventListener = function removeEventListener() {
					var
						element = this,
						type = arguments[ 0 ],
						listener = arguments[ 1 ],
						index;

					if (element._events && element._events[ type ] &&
						element._events[ type ].list) {
						index = indexOf( element._events[ type ].list, listener );

						if (index !== - 1) {
							element._events[ type ].list.splice( index, 1 );

							if (!element._events[ type ].list.length) {
								if (element.detachEvent) {
									element.detachEvent( 'on' + type, element._events[ type ] );
								}
								delete element._events[ type ];
							}
						}
					}
				};

				window.dispatchEvent = Window.prototype.dispatchEvent = Document.prototype.dispatchEvent = Element.prototype.dispatchEvent = function dispatchEvent( event ) {
					if (!arguments.length) {
						throw new Error( 'Not enough arguments' );
					}

					if (!event || typeof event.type !== 'string') {
						throw new Error( 'DOM Events Exception 0' );
					}

					var element = this, type = event.type;

					try {
						if (!event.bubbles) {
							event.cancelBubble = true;

							var cancelBubbleEvent = function( event ) {
								event.cancelBubble = true;

								( element || window ).detachEvent( 'on' + type,
									cancelBubbleEvent );
							};

							this.attachEvent( 'on' + type, cancelBubbleEvent );
						}

						this.fireEvent( 'on' + type, event );
					}
					catch ( error ) {
						event.target = element;

						do {
							event.currentTarget = element;

							if ('_events' in element && typeof element._events[ type ] ===
								'function') {
								element._events[ type ].call( element, event );
							}

							if (typeof element[ 'on' + type ] === 'function') {
								element[ 'on' + type ].call( element, event );
							}

							element = element.nodeType === 9
								? element.parentWindow
								: element.parentNode;
						} while (element && !event.cancelBubble);
					}

					return true;
				};

				// Add the DOMContentLoaded Event
				document.attachEvent( 'onreadystatechange', function() {
					if (document.readyState === 'complete') {
						document.dispatchEvent( new Event( 'DOMContentLoaded', {
							bubbles: true,
						} ) );
					}
				} );
			}
		}() );

	}

	if (!( 'CustomEvent' in this && ( 'function' == typeof this.CustomEvent ||
			this.CustomEvent.toString().indexOf( 'CustomEventConstructor' ) > - 1 )
	)) {

// CustomEvent
		this.CustomEvent = function CustomEvent( type, eventInitDict ) {
			if (!type) {
				throw Error(
					'TypeError: Failed to construct "CustomEvent": An event name must be provided.' );
			}

			var event;
			eventInitDict = eventInitDict ||
				{ bubbles: false, cancelable: false, detail: null };

			if ('createEvent' in document) {
				try {
					event = document.createEvent( 'CustomEvent' );
					event.initCustomEvent( type, eventInitDict.bubbles,
						eventInitDict.cancelable, eventInitDict.detail );
				}
				catch ( error ) {
					// for browsers which don't support CustomEvent at all, we
					// use a regular event instead
					event = document.createEvent( 'Event' );
					event.initEvent( type, eventInitDict.bubbles,
						eventInitDict.cancelable );
					event.detail = eventInitDict.detail;
				}
			}
			else {

				// IE8
				event = new Event( type, eventInitDict );
				event.detail = eventInitDict && eventInitDict.detail || null;
			}
			return event;
		};

		CustomEvent.prototype = Event.prototype;

	}

	if (!( 'matchMedia' in this && 'MediaQueryList' in this
	)) {

// matchMedia
		( function( global ) {
			function evalQuery( query ) {
				/* jshint evil: true */
				query = ( query || 'true' ).replace( /^only\s+/, '' ).
					replace( /(device)-([\w.]+)/g, '$1.$2' ).
					replace( /([\w.]+)\s*:/g, 'media.$1 ===' ).
					replace( /min-([\w.]+)\s*===/g, '$1 >=' ).
					replace( /max-([\w.]+)\s*===/g, '$1 <=' ).
					replace( /all|screen/g, '1' ).
					replace( /print/g, '0' ).
					replace( /,/g, '||' ).
					replace( /\band\b/g, '&&' ).
					replace( /dpi/g, '' ).
					replace( /(\d+)(cm|em|in|dppx|mm|pc|pt|px|rem)/g,
						function( $0, $1, $2 ) {
							return $1 * (
								$2 === 'cm' ? 0.3937 * 96 : (
									$2 === 'em' || $2 === 'rem' ? 16 : (
										$2 === 'in' || $2 === 'dppx' ? 96 : (
											$2 === 'mm' ? 0.3937 * 96 / 10 : (
												$2 === 'pc' ? 12 * 96 / 72 : (
													$2 === 'pt' ? 96 / 72 : 1
												)
											)
										)
									)
								)
							);
						} );
				return new Function( 'media',
					'try{ return !!(%s) }catch(e){ return false }'.replace( '%s', query )
				)( {
					width: global.innerWidth,
					height: global.innerHeight,
					orientation: global.orientation || 'landscape',
					device: {
						width: global.screen.width,
						height: global.screen.height,
						orientation: global.screen.orientation || global.orientation ||
							'landscape'
					}
				} );
			}

			function MediaQueryList() {
				this.matches = false;
				this.media = 'invalid';
			}

			MediaQueryList.prototype.addListener = function addListener( listener ) {
				var listenerIndex = this.addListener.listeners.indexOf( listener );

				if (listenerIndex === - 1) {
					this.addListener.listeners.push( listener );
				}
			};

			MediaQueryList.prototype.removeListener = function removeListener( listener ) {
				var listenerIndex = this.addListener.listeners.indexOf( listener );

				if (listenerIndex >= 0) {
					this.addListener.listeners.splice( listenerIndex, 1 );
				}
			};

			global.MediaQueryList = MediaQueryList;

			// <Global>.matchMedia
			global.matchMedia = function matchMedia( query ) {
				var
					list = new MediaQueryList();

				if (0 === arguments.length) {
					throw new TypeError( 'Not enough arguments to matchMedia' );
				}

				list.media = String( query );
				list.matches = evalQuery( list.media );
				list.addListener.listeners = [];

				global.addEventListener( 'resize', function() {
					var listeners = [].concat( list.addListener.listeners ),
						matches = evalQuery( list.media );

					if (matches != list.matches) {
						list.matches = matches;
						for (var index = 0, length = listeners.length; index <
						length; ++ index) {
							listeners[ index ].call( global, list );
						}
					}
				} );

				return list;
			};
		}( this ) );

	}

} ).call(
	'object' === typeof window && window || 'object' === typeof self && self ||
	'object' === typeof global && global || {} );
