/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.9
 * @since        Available since Release 4.0
 */

var CustomEffect = {
    _elementDoesNotExistError: {
        name: 'ElementDoesNotExistError',
        message: 'The specified DOM element does not exist, but is required for this effect to operate'
    },
    tagifyText: function(element) {
        if(typeof Builder == 'undefined')
            throw("Effect.tagifyText requires including script.aculo.us' builder.js library");

        var tagifyStyle = 'position:relative';
        if(Prototype.Browser.IE) tagifyStyle += ';zoom:1';

        element = $(element);
        $A(element.childNodes).each( function(child) {
            if(child.nodeType==3) {
                child.nodeValue.toArray().each( function(character) {
                    element.insertBefore(
                        Builder.node('span',{style: tagifyStyle},
                            character == ' ' ? String.fromCharCode(160) : character),
                        child);
                });
                Element.remove(child);
            }
        });
    },
    multiple: function(element, effect) {
        var elements;
        if(((typeof element == 'object') ||
            (typeof element == 'function')) &&
            (element.length))
            elements = element;
        else
            elements = $(element).childNodes;

        var options = Object.extend({
            speed: 0.1,
            delay: 0.0
        }, arguments[2] || {});
        var masterDelay = options.delay;

        $A(elements).each( function(element, index) {
            new effect(element, Object.extend(options, { delay: index * options.speed + masterDelay }));
        });
    },
    PAIRS: {
        'slide':  ['SlideDown','SlideUp'],
        'blind':  ['BlindDown','BlindUp'],
        'appear': ['Appear','Fade']
    },
    toggle: function(element, effect) {
        element = $(element);
        effect = (effect || 'appear').toLowerCase();
        var options = Object.extend({
            queue: { position:'end', scope:(element.id || 'global'), limit: 1 }
        }, arguments[2] || {});
        Effect[element.visible() ?
            Effect.PAIRS[effect][1] : Effect.PAIRS[effect][0]](element, options);
    }
};

CustomEffect.Base = Class.create({
    position: null,
    start: function(options) {
        function codeForEvent(options,eventName){
            return (
                (options[eventName+'Internal'] ? 'this.options.'+eventName+'Internal(this);' : '') +
                    (options[eventName] ? 'this.options.'+eventName+'(this);' : '')
                );
        }
        if (options && options.transition === false) options.transition = Effect.Transitions.linear;
        this.options      = Object.extend(Object.extend({ },Effect.DefaultOptions), options || { });
        this.currentFrame = 0;
        this.state        = 'idle';
        this.startOn      = this.options.delay*1000;
        this.finishOn     = this.startOn+(this.options.duration*1000);
        this.fromToDelta  = this.options.to-this.options.from;
        this.totalTime    = this.finishOn-this.startOn;
        this.totalFrames  = this.options.fps*this.options.duration;

        this.render = (function() {
            function dispatch(effect, eventName) {
                if (effect.options[eventName + 'Internal'])
                    effect.options[eventName + 'Internal'](effect);
                if (effect.options[eventName])
                    effect.options[eventName](effect);
            }

            return function(pos) {
                if (this.state === "idle") {
                    this.state = "running";
                    dispatch(this, 'beforeSetup');
                    if (this.setup) this.setup();
                    dispatch(this, 'afterSetup');
                }
                if (this.state === "running") {
                    pos = (this.options.transition(pos) * this.fromToDelta) + this.options.from;
                    this.position = pos;
                    dispatch(this, 'beforeUpdate');
                    if (this.update) this.update(pos);
                    dispatch(this, 'afterUpdate');
                }
            };
        })();

        this.event('beforeStart');
        if (!this.options.sync)
            Effect.Queues.get(Object.isString(this.options.queue) ?
                'global' : this.options.queue.scope).add(this);
    },
    loop: function(timePos) {
        if (timePos >= this.startOn) {
            if (timePos >= this.finishOn) {
                this.render(1.0);
                this.cancel();
                this.event('beforeFinish');
                if (this.finish) this.finish();
                this.event('afterFinish');
                return;
            }
            var pos   = (timePos - this.startOn) / this.totalTime,
                frame = (pos * this.totalFrames).round();
            if (frame > this.currentFrame) {
                this.render(pos);
                this.currentFrame = frame;
            }
        }
    },
    cancel: function() {
        if (!this.options.sync)
            Effect.Queues.get(Object.isString(this.options.queue) ?
                'global' : this.options.queue.scope).remove(this);
        this.state = 'finished';
    },
    event: function(eventName) {
        if (this.options[eventName + 'Internal']) this.options[eventName + 'Internal'](this);
        if (this.options[eventName]) this.options[eventName](this);
    },
    inspect: function() {
        var data = $H();
        for(property in this)
            if (!Object.isFunction(this[property])) data.set(property, this[property]);
        return '#<Effect:' + data.inspect() + ',options:' + $H(this.options).inspect() + '>';
    }
});

CustomEffect.Tween = Class.create(CustomEffect.Base, {
    initialize: function(object, from, to) {
        object = Object.isString(object) ? $(object) : object;
        var args = $A(arguments), method = args.last(),
            options = args.length == 5 ? args[3] : null;
        this.method = Object.isFunction(method) ? method.bind(object) :
            Object.isFunction(object[method]) ? object[method].bind(object) :
                function(value) { object[method] = value };
        this.start(Object.extend({ from: from, to: to }, options || { }));
    },
    update: function(position) {
        this.method(position);
    }
});