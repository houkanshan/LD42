var escapes = {
  '\\': '\\',
  "'": "'",
  'r': '\r',
  'n': '\n',
  't': '\t',
  'u2028': '\u2028',
  'u2029': '\u2029'
}

for (var p in escapes) {
  escapes[escapes[p]] = p
}

var escaper = /\\|'|\r|\n|\t|\u2028|\u2029/g
var unescaper = /\\(\\|'|r|n|t|u2028|u2029)/g

// Within an interpolation, evaluation, or escaping, remove HTML escaping
// that had been previously added.
var unescape = function(code) {
  return code.replace(unescaper, function(match, escape) {
    return escapes[escape]
  })
}
var escape = function(str) {
    str = '' + str || ''
    var xmlchar = {
      '&': '&amp;'
    , '<': '&lt;'
    , '>': '&gt;'
    , "'": '&#39;'
    , '"': '&quot;'
    }

    return str.replace(/[<>&'"]/g, function($1) {
      return xmlchar[$1]
    })
  }

// JavaScript micro-templating, similar to John Resig's implementation.
// Underscore templating handles arbitrary delimiters, preserves whitespace,
// and correctly escapes quotes within interpolated code.
var template = function(text, data, settings) {
  settings = {
    evaluate : /\{\{([\s\S]+?)\}\}/g,
    interpolate : /\{\{=([\s\S]+?)\}\}/g,
    escape : /\{\{-([\s\S]+?)\}\}/g
  }

  // Compile the template source, taking care to escape characters that
  // cannot be included in a string literal and then unescape them in code
  // blocks.
  var source = "__p+='" + text
    .replace(escaper, function(match) {
      return '\\' + escapes[match]
    })
    .replace(settings.escape, function(match, code) {
      return "'+\nescape(" + unescape(code) + ")+\n'"
    })
    .replace(settings.interpolate, function(match, code) {
      return "'+\n(" + unescape(code) + ")+\n'"
    })
    .replace(settings.evaluate, function(match, code) {
      return "';\n" + unescape(code) + "\n;__p+='"
    }) + "';\n"

  // If a variable is not specified, place data values in local scope.
  if (!settings.variable) {
    source = 'with(obj||{}){\n' + source + '}\n'
  }

  source = "var __p='';" +
    "var print=function(){__p+=Array.prototype.join.call(arguments, '')};\n" +
    source + 'return __p;\n'

  var render = new Function(settings.variable || 'obj', 'escape', source)
  if (data) { return render(data, escape) }
  var tmpl = function(_data) {
    return render.call(this, _data, escape)
  }

  // Provide the compiled function source as a convenience for build time
  // precompilation.
  tmpl.source = 'function(' + (settings.variable || 'obj') + '){\n' +
    source + '}'

  return tmpl
}

export default template