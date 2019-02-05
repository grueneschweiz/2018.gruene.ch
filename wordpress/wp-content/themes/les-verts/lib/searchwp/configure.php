<?php

function supt_configure_searchwp() {
	if (!class_exists('SearchWP')){
		return;
	}

	$settings = '{"default":{"post":{"enabled":true,"weights":{"tax":{"category":50,"post_tag":50,"post_format":0,"language":0,"post_translations":0},"title":80,"content":5,"excerpt":40,"slug":60,"cf":{"swppv4dff695361f02e59e1755d73c44613e1":{"metakey":"teaser","weight":40},"swppv86f04dd95e8b95800b95b29380072e36":{"metakey":"main_content_content_%_title","weight":10},"swppv6b4ab4b4c4ea4e7d54099b7216dfa9c3":{"metakey":"main_content_content_%_content_%_text","weight":20},"swppva837378ee57c5fccd17758a111160dca":{"metakey":"main_content_content_%_text","weight":5},"swppv9203ff8be19c77794f562c8b26f40cb9":{"metakey":"main_content_content_%_quote","weight":5},"swppvec57f747361d11fb4add893f27de3742":{"metakey":"main_content_content_%_role","weight":5},"swppve7cbaf7b0a05d8cc2d94a9fedc07cf0b":{"metakey":"main_content_content_%_person","weight":5}},"comment":1},"options":{"exclude":"","attribute_to":"0","parent":"0","stem":"0"}},"page":{"enabled":true,"weights":{"tax":{"language":0,"post_translations":0},"title":80,"content":5,"slug":60,"cf":{"swppv53ea7e950a6b32e7585dfa8f745b025d":{"metakey":"teaser","weight":40},"swppv42f8699987aacd93c09eafe9f6550e5e":{"metakey":"main_content_content_%_title","weight":10},"swppvfcea47b1d56e21251f67ff96cb0b58d7":{"metakey":"main_content_content_%_person","weight":5},"swppv7143a1a9828be6fd165e559a0fd78a98":{"metakey":"main_content_content_%_role","weight":5},"swppvbe743b2e9c53a876677274a63036ce9a":{"metakey":"main_content_content_%_quote","weight":5},"swppv8145aedbb3ef4f244d5131e5feabe780":{"metakey":"main_content_content_%_text","weight":5},"swppv1424c08a4dbe956361c163c404450796":{"metakey":"main_content_content_%_content_%_text","weight":20}},"comment":1},"options":{"exclude":"","attribute_to":"0","parent":"0","stem":"0"}},"tribe_events":{"enabled":true,"weights":{"tax":{"post_tag":0,"language":0,"post_translations":0,"tribe_events_cat":0},"title":80,"content":5,"excerpt":40,"slug":60,"cf":{"swppvcbc5be1c4083ea4a3beba9b9a280a2a5":{"metakey":"description","weight":5}},"comment":1},"options":{"exclude":"","attribute_to":"0","parent":"0","stem":"0"}},"attachment":{"enabled":false,"weights":{"tax":{"media_category":0},"title":80,"content":5,"excerpt":40,"slug":60},"options":{"exclude":"","attribute_to":"0","parent":"0","stem":"0"}},"people":{"enabled":false,"weights":{"tax":{"testimonials":0},"title":80,"slug":60},"options":{"exclude":"","attribute_to":"0","parent":"0","stem":"0"}}}}';

	$searchwp = SearchWP::instance();
	$searchwp->import_settings($settings);
}

supt_configure_searchwp();
