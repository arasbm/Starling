from nltk import word_tokenize,WordNetLemmatizer,NaiveBayesClassifier,classify,MaxentClassifier
from nltk.corpus import stopwords
import random, os, glob, re, string, time, sys
import tweepy
from textwrap import TextWrapper

# == OAuth Authentication ==
# This mode of authentication is the new preferred way
# of authenticating with Twitter.

# The consumer keys can be found on your application's Details
# page located at https://dev.twitter.com/apps (under "OAuth settings")
#Twitter keys
consumer_key="your_consumer_key"
consumer_secret="your_consumer_secret"

# The access tokens can be found on your applications's Details
# page located at https://dev.twitter.com/apps (located
# under "Your access token")
access_token="your_access_token"
access_token_secret="your_access_token_secret="

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_token, access_token_secret)

gfp = open('tweets/geo-flu.txt', 'a+')
ngfp = open('tweets/ngeo-flu.txt', 'a+')
lfp = open("tweets/labelled_tweets.txt", 'a+')

commonwords = stopwords.words('english')
wordlemmatizer = WordNetLemmatizer()

class StreamListener(tweepy.StreamListener):
    status_wrapper = TextWrapper(width=60, initial_indent='    ', subsequent_indent='    ')
    
    def on_status(self, status):
        try:
            if  status.coordinates is not None:
                gfp.write(str(status.created_at) + ';@;' +
                          str(status.coordinates['coordinates'][0]) + ';@;' +
                          str(status.coordinates['coordinates'][1]) + ';@;' +
                          str(status.author.screen_name) + ';@;' +
                          str(status.source)  + ';@;' +  str(status.text) +
                          '\n')
                raw_tweet_text_ = ''
                time_ = str(status.created_at)
                raw_tweet_text_ = str(status.text)
                tweet_text = re.sub(r'\s*http://.+\s*', '', raw_tweet_text_)
                if tweet_text != '':
                    featset = tweet_features(tweet_text)
                    #compute the label
                    label_val = classifier.classify(featset)
                    prob_val = classifier.prob_classify(featset)
                    lfp.write(time_ + ';@;' + str(status.coordinates['coordinates'][1]) + ';@;' +
                                str(status.coordinates['coordinates'][0]) +
                                ';@;' + str(status.text) + ';@;' +
                                label_val + ';@;' +str(prob_val.prob('1')) + "\n")
            else:
                ngfp.write(str(status.created_at) + ';@;'+
                           str(status.author.screen_name) + ';@;' +
                           str(status.source)  + ';@;' + str(status.text) +
                           '\n')
            gfp.flush()
            ngfp.flush()
            lfp.flush()
        except Exception, e:
            pass

def tweet_features(sent):
	features = {}
	wordtokens = [wordlemmatizer.lemmatize(word.lower()) for word in word_tokenize(sent)]
	for word in wordtokens:
		if word not in commonwords:
			features[word] =  True
	return features

postexts  = []
negtexts  = []

fp = open("tweets/training.txt")
lines = fp.readlines()

for line in lines:
    tokens = string.split(line, ';@;')
    tweet_txt = tokens[0]
    label = tokens[1]
    if int(label) == 1:
	    postexts.append(tweet_txt)
    elif int(label) == 0:
        negtexts.append(tweet_txt)
fp.close()

mixedtweets =	([(tweet,'-1') for tweet in negtexts] + [(tweet,'1') for tweet in postexts])
	
random.shuffle(mixedtweets)
featuresets = [(tweet_features(n), g) for (n,g) in mixedtweets]

size = int(len(featuresets) * 1.0)

#train_set, test_set = featuresets[size:], featuresets[:size]
train_set = featuresets	#use the whole feature set for training 
classifier = NaiveBayesClassifier.train(train_set)

#classifier = MaxentClassifier.train(train_set,'Powell',3)

#classifier.show_most_informative_features(30)
#print 'accuracy: ', classify.accuracy(classifier,test_set)
#classifier.show_most_informative_features(30)
#print 'labels:',classifier.labels()
#while(1):
#  featset = tweet_features(raw_input("Enter a tweet to classify: "))
#  print classifier.classify(featset)
streamer = tweepy.Stream(auth=auth, listener=StreamListener(),
                         timeout=3000000000 )
print streamer
setTerms = ['vaccination', 'Vaccination', 'epidemic', 'Epidemic', 'flu', 'Flu',
            'symptom', 'Symptom', 'vaccine', 'Vaccine', 'sick', 'Sick',
            'illness', 'Illness', 'influenza', 'Influenza',
            'flu shot', 'Flu shot', 'virus', 'Virus', 'fever',
            'Fever', 'headache', 'Headache', 'sore throat', 'Sore throat',
            'coughing', 'Coughing', 'sneeze', 'Sneeze',
            'sneezing', 'Sneezing', 'infected', 'Infected',
            'epidemy', 'Epidemy', 'medicine', 'Medicine',
            'contagion', 'Contagion', 'contagious', 'Contagious',
            'runny nose', 'Runny nose']

streamer.filter(None,setTerms)
streamer.sample()
