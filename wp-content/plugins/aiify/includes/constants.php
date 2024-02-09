<?php

namespace AIIFY;

define( 'AIIFY_TOKEN_WORD_RATIO', 0.6 );
define( 'AIIFY_WRITING_DEFAULT_LANGUAGE', 'English (United States)' );


// Availabe chat models
define(
	'AIIFY_AI_ENGINES',
	array(
		'openai'     => 'Open Ai : ChatGpt-3.5, ChatGpt-4',
		'ollama'     => 'Ollama : Llama 2, Phi-2, etc...',
		'openrouter' => 'OpenRouter : Claude, Gemini, Mistral, GPTs, etc…',
	// "text-davinci-003" => 'text-davinci-003' ( disabled completions for now as prompting is not really compatible )
	)
);

// Availabe chat models
define(
	'AIIFY_CHAT_MODELS',
	array(
		'gpt-3.5-turbo' => 'gpt-3.5-turbo',
		'gpt-4'         => 'gpt-4',
	// "text-davinci-003" => 'text-davinci-003' ( disabled completions for now as prompting is not really compatible )
	)
);

// Those prompt are use facing prompts, they need to be translated
define(
	'AIIFY_WRITER_PROMPTS',
	array(
		''                                               => '',
		__( 'Write a blog post about', 'aiify' )         => __( 'a topic', 'aiify' ),
		__( 'Write a press release about', 'aiify' )     => __( 'a news event', 'aiify' ),
		__( 'Write a social media post about', 'aiify' ) => __( 'a specific aspect of your product or service', 'aiify' ),
		__( 'Write a product description for', 'aiify' ) => __( 'a specific retailer or audience', 'aiify' ),
		__( 'Write an email newsletter about', 'aiify' ) => __( 'news or updates related to your business', 'aiify' ),
		__( 'Write website copy for', 'aiify' )          => __( 'a specific page or section of your website', 'aiify' ),
		__( 'Write a video script for', 'aiify' )        => __( 'a promotional or instructional video', 'aiify' ),
		__( 'Write a whitepaper on', 'aiify' )           => __( 'a particular topic or industry trend', 'aiify' ),
		__( 'Write a case study about', 'aiify' )        => __( 'a specific customer or business challenge', 'aiify' ),
		__( 'Write an e-book on', 'aiify' )              => __( 'a specific topic or industry', 'aiify' ),
		__( 'Write an infographic about', 'aiify' )      => __( 'a specific topic or data set', 'aiify' ),
		__( 'Write a sales letter for', 'aiify' )        => __( 'a specific product or service', 'aiify' ),
		__( 'Write a landing page for', 'aiify' )        => __( 'a specific offer or promotion', 'aiify' ),
		__( 'Write a product review for', 'aiify' )      => __( 'a specific product or service', 'aiify' ),
		__( 'Write a how-to guide for', 'aiify' )        => __( 'a specific task or process', 'aiify' ),
		__( 'Write a listicle about', 'aiify' )          => __( 'a specific topic or set of related items', 'aiify' ),
		__( 'Write a research paper on', 'aiify' )       => __( 'a specific topic or question', 'aiify' ),
		__( 'Write a personal essay about', 'aiify' )    => __( 'a personal experience or perspective', 'aiify' ),
		__( 'Write a news article about', 'aiify' )      => __( 'a specific news event', 'aiify' ),
		__( 'Write a feature article about', 'aiify' )   => __( 'a specific person, place, or thing', 'aiify' ),
		__( 'Write a company profile about', 'aiify' )   => __( 'a specific business or organization', 'aiify' ),
		__( 'Write a guest post for', 'aiify' )          => __( 'a specific blog or publication', 'aiify' ),
		__( 'Write a comparison article on', 'aiify' )   => __( 'two or more products or services', 'aiify' ),
		__( 'Write an interview article with', 'aiify' ) => __( 'a specific person or expert', 'aiify' ),
		__( 'Write an opinion piece on', 'aiify' )       => __( 'a specific topic or issue', 'aiify' ),
		__( 'Write an article outline for', 'aiify' )    => __( 'a specific topic or article idea', 'aiify' ),
	)
);

// Edit prompt are always formulated in english, avoid having to translate
define(
	'AIIFY_EDIT_PROMPTS',
	array(
		__( 'Fix spelling and grammar', 'aiify' ) => 'Identify and correct any spelling or grammar errors in the text.',
		__( 'Simplify language', 'aiify' )        => 'Simplify the language used in the text, making it easier to understand for a wider audience.',
		__( 'Make longer', 'aiify' )              => 'Add additional content to the text to make it longer while still being relevant to the main topic.',
		__( 'Make shorter', 'aiify' )             => 'Make the text shorter and more concise by reducing its length to half. Focus on maintaining the key details and essence.',
		__( 'Improve writing', 'aiify' )          => 'Make improvements to the writing style and structure of the text to make it more engaging and professional.',
		__( 'Paraphrase', 'aiify' )               => 'Rewrite sections of the text to convey the same meaning but in different words or sentence structures.',
		__( 'Add more detail', 'aiify' )          => "Identify areas of the text where more detail would improve the reader's understanding and add additional information.",
		__( 'Remove redundancy', 'aiify' )        => 'Identify and remove any repetitive or unnecessary information from the text.',
		__( 'Clarify meaning', 'aiify' )          => 'Make improvements to the clarity and coherence of the text to ensure that the meaning is clear and easy to understand.',
		__( 'Strengthen argument', 'aiify' )      => 'Evaluate the argument presented in the text and make improvements to strengthen it or address any weaknesses.',
		__( 'Emphasize keywords', 'aiify' )       => 'Before printing the final anszer, follow those exact steps :
1. Identify the main topic of the text.
2. Count the total words of the text then calculate 7% of this total, this calculated number will be our {MAX}.
3. Identify the most important keywords related to the main topic of the text and ensure they are not common words.
4. Reduce this list of keywords to the {MAX} you evaluated in step 2 by keeping only the most important ones, this reduced list will be {THE_FINAL_LIST}.
5. Emphasize only the {THE_FINAL_LIST} that resulted from step 4 in the input text in markdown, using ** to bolden them, and make sure not to emphasize any other keywords or common words.

Please note that the expected result is ONLY the same provided text with {THE_FINAL_LIST} emphasized in markdown (**). Do not print the explanations.',
	)
);

define(
	'AIIFY_GENERATE_BEFORE_PROMPTS',
	array(
		__( 'Heading', 'aiify' )      => 'Produce a heading for the following text.',
		__( 'Tagline', 'aiify' )      => 'Craft a tagline for the following text.',
		__( 'Introduction', 'aiify' ) => 'Write an introduction for the following text.',
		__( 'Summary', 'aiify' )      => 'Create a summary for the following text.',
	)
);

define(
	'AIIFY_GENERATE_AFTER_PROMPTS',
	array(
		__( 'Summarize', 'aiify' )                     => 'Summarize the following text.',
		__( 'List Key Takeaways', 'aiify' )            => 'List key takeaways from the following text without heading.',
		__( 'Find Action Items', 'aiify' )             => 'Extract action items from the following text.',
		__( 'Explain', 'aiify' )                       => 'Explain the following text.',
		__( 'Elaborate', 'aiify' )                     => 'Elaborate on the following text.',
		__( 'Find Main Idea', 'aiify' )                => 'Find the main idea of the following text.',
		__( 'Provide Examples', 'aiify' )              => 'Provide examples based on the following text.',
		__( 'Complete with sources', 'aiify' )         => 'Complete the following text making sure to add sources and quotations to support your statements.',
		__( 'Evaluate and Provide Feedback', 'aiify' ) => 'Evaluate the following text and provide feedback.',
		__( 'Write a Conclusion', 'aiify' )            => 'Write a conclusion for the following text.',
	)
);

define(
	'AIIFY_STYLES',
	array(
		'Journalistic'   => __( 'Journalistic', 'aiify' ),
		'Academic'       => __( 'Academic', 'aiify' ),
		'Creative'       => __( 'Creative', 'aiify' ),
		'Technical'      => __( 'Technical', 'aiify' ),
		'Business'       => __( 'Business', 'aiify' ),
		'Scientific'     => __( 'Scientific', 'aiify' ),
		'Casual'         => __( 'Casual', 'aiify' ),
		'Formal'         => __( 'Formal', 'aiify' ),
		'Narrative'      => __( 'Narrative', 'aiify' ),
		'Descriptive'    => __( 'Descriptive', 'aiify' ),
		'Persuasive'     => __( 'Persuasive', 'aiify' ),
		'Expository'     => __( 'Expository', 'aiify' ),
		'Analytical'     => __( 'Analytical', 'aiify' ),
		'Critical'       => __( 'Critical', 'aiify' ),
		'Conversational' => __( 'Conversational', 'aiify' ),
		'Professional'   => __( 'Professional', 'aiify' ),
		'Humorous'       => __( 'Humorous', 'aiify' ),
		'Instructional'  => __( 'Instructional', 'aiify' ),
		'Inspirational'  => __( 'Inspirational', 'aiify' ),
		'Motivational'   => __( 'Motivational', 'aiify' ),
	)
);


define(
	'AIIFY_TONES',
	array(
		'Professional'               => __( 'Professional', 'aiify' ),
		'Cheerful'                   => __( 'Cheerful', 'aiify' ),
		'Excited'                    => __( 'Excited', 'aiify' ),
		'Optimistic'                 => __( 'Optimistic', 'aiify' ),
		'Confident'                  => __( 'Confident', 'aiify' ),
		'Sarcastic'                  => __( 'Sarcastic', 'aiify' ),
		'Sincere'                    => __( 'Sincere', 'aiify' ),
		'Sympathetic'                => __( 'Sympathetic', 'aiify' ),
		'Concerned'                  => __( 'Concerned', 'aiify' ),
		'Caring'                     => __( 'Caring', 'aiify' ),
		'Neutral'                    => __( 'Neutral', 'aiify' ),
		'Formal'                     => __( 'Formal', 'aiify' ),
		'Authoritative'              => __( 'Authoritative', 'aiify' ),
		'Serious'                    => __( 'Serious', 'aiify' ),
		'Humorous'                   => __( 'Humorous', 'aiify' ),
		'Playful'                    => __( 'Playful', 'aiify' ),
		'Friendly'                   => __( 'Friendly', 'aiify' ),
		'Warm'                       => __( 'Warm', 'aiify' ),
		'Cold'                       => __( 'Cold', 'aiify' ),
		'Impersonal'                 => __( 'Impersonal', 'aiify' ),
		'Informative/Educational'    => __( 'Informative/Educational', 'aiify' ),
		'Conversational'             => __( 'Conversational', 'aiify' ),
		'Professional/Formal'        => __( 'Professional/Formal', 'aiify' ),
		'Authoritative'              => __( 'Authoritative', 'aiify' ),
		'Persuasive'                 => __( 'Persuasive', 'aiify' ),
		'Inspirational/Motivational' => __( 'Inspirational/Motivational', 'aiify' ),
		'Entertaining'               => __( 'Entertaining', 'aiify' ),
		'Controversial'              => __( 'Controversial', 'aiify' ),
		'Humorous'                   => __( 'Humorous', 'aiify' ),
		'Empathetic'                 => __( 'Empathetic', 'aiify' ),
		'Personal'                   => __( 'Personal', 'aiify' ),
		'Storytelling'               => __( 'Storytelling', 'aiify' ),
		'Thought-provoking'          => __( 'Thought-provoking', 'aiify' ),
		'Authentic'                  => __( 'Authentic', 'aiify' ),
		'Trustworthy'                => __( 'Trustworthy', 'aiify' ),
		'Authorial'                  => __( 'Authorial', 'aiify' ),
		'Collaborative'              => __( 'Collaborative', 'aiify' ),
		'Problem-solving'            => __( 'Problem-solving', 'aiify' ),
		'Encouraging'                => __( 'Encouraging', 'aiify' ),
		'Straightforward'            => __( 'Straightforward', 'aiify' ),
		'Optimistic'                 => __( 'Optimistic', 'aiify' ),
		'Analytical'                 => __( 'Analytical', 'aiify' ),
		'Objective'                  => __( 'Objective', 'aiify' ),
		'Critical'                   => __( 'Critical', 'aiify' ),
		'Simplified'                 => __( 'Simplified', 'aiify' ),
		'Complex'                    => __( 'Complex', 'aiify' ),
		'Curious'                    => __( 'Curious', 'aiify' ),
		'Inspiring'                  => __( 'Inspiring', 'aiify' ),
		'Motivating'                 => __( 'Motivating', 'aiify' ),
	)
);


define( 'AIIFY_PARAGRAPH_BLOCK_PROMPT', __( 'Type "AI+Enter" for AI, or "/" to choose a block', 'aiify' ) );


// System Formatting defaults
define( 'AIIFY_SYSTEM_PROMPT_DEFAULT', "Ignore all previous instructions.\n\nAs an advanced AI language model, you already possess a deep understanding of the basic principles of search engine optimization and Copywriting. As an expert Copywriter, you know the importance of crafting unique, high-quality content that engages readers and drives traffic to websites. You also understand the key factors that impact search engine rankings, allowing you to optimize content for both search engines and human readers." ); // To further enhance your skills as a professional SEO expert and Copywriter, let's review some of these basic principles

define( 'AIIFY_SYSTEM_EDIT_PROMPT_DEFAULT', "Ignore all previous instructions.\n\nYou are an expert Copywriter focused on editing the provided material." );

define(
	'AIIFY_SYSTEM_PROMPT_FORMATING_DEFAULT',
	'Important Formating rules to follow:
As a general rule, use markdown for formatting and organize your content to help readers navigate it easily : 
1. Headings: Please start with level 2 headings (##) followed by a space to indicate different sections, avoid using the alternate (==) underlined format for headings, example : ## My heading.
2. Bold: Enclose relevant keywords related to the main topic with double asterisks to make them bold, example : **my keyword**.
3. Italics: Use single asterisks (text) to italicize the appropriate keywords or phrases like *this*.
4. Lists: Utilize hyphens (-) followed by a space for unordered list items and numbers (1., 2., 3., etc.) followed by a space for ordered list items.
5. Quotes: Begin each quote with a greater than symbol (>) followed by a space and the quoted text.
6. Do not add (```markdown) to specify your answer is in Markdown.
Those are great techniques to make your content more readable and engaging.
Follow those principles, when appropriate, as much as you can.'
);
define(
	'AIIFY_SYSTEM_EDIT_PROMPT_FORMATING_DEFAULT',
	'Use markdown for formatting your content to help readers navigate it easily : 
1. Bold: Enclose relevant keywords related to the main topic with double asterisks to make them bold, example : **my keyword**.
2. Italics: Use single asterisks (text) to italicize the appropriate keywords or phrases like *this*.
3. Lists: Utilize hyphens (-) followed by a space for unordered list items and numbers (1., 2., 3., etc.) followed by a space for ordered list items.
4. Do not add (```markdown) to specify your answer is in Markdown.'
);

define( 'AIIFY_SYSTEM_INSTRUCTION_HEADER_DEFAULT', 'Style: {style}. Tone: {tone}. It is very important that your response must be formatted in Markdown and highlighting keywords.' );



define(
	'AIIFY_SYSTEM_PROMPT_STRUCTURE_DEFAULT',
	'[{header} Do your best to create unique content free of plagiarism that respects the expected Markdown formatting (headings, lists, bold, italics, bold, quotes)]

{#context}
First, here is some important facts to include in your answer :
{context}
{/context}

Now, here is the task, while following all the previous instructions, respond in "{language}" language in less than {maxWords} words {#keywords} and make sure to utilize and **bold** every one of the following comma seperated keywords: "{keywords}"{/keywords}.

"""
{prompt}.
"""
'
);


define(
	'AIIFY_SYSTEM_EDIT_STRUCTURE_DEFAULT',
	'[Consider the input text formated in Markdown or HTML and make sure to respect the expected Markdown formatting.]

It is very important to not write explanations. Do not echo my prompt. Do not remind me what I asked you for. Do not apologize. Do not self-reference. Do not use generic filler phrases. Get to the point precisely and accurately. Do not explain what and why, just give me your best possible result.

The task: {command} Respond in {language} in less than {maxWords} words

"""
{edit}
"""
'
);
