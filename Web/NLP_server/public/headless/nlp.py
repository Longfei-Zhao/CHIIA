from pipeline import getArticleByID
from Dataset import relevant,irrelevant,dataset,stopwords
#Test for get Article from database
#print(getArticleByID('CANBTZ0020141022eaan00011'))
relevant_articles = list()
irrelevant_articles = list()
for id in relevant:
	article = getArticleByID(id)
	if article:
		relevant_articles.append(article)
		#print(article['ID'],article['HD'])
print('{} relevant articles in given set, found {} of them in database'.format(len(relevant),len(relevant_articles))) 
for id in irrelevant:
	article = getArticleByID(id)
	if article:
		irrelevant_articles.append(article)
	#	print(article['ID'],article['HD'])
print('{} irrelevant articles in given set, found {} of them in database'.format(len(irrelevant),len(irrelevant_articles)))



import warnings

with warnings.catch_warnings():
	warnings.filterwarnings("ignore")
	import sklearn as sk
	import numpy as np

news = dataset

SPLIT_PERC = 0.75
split_size = int(len(news.data)*SPLIT_PERC)
X_train = news.data[:split_size]
X_test = news.data[split_size:]
y_train = news.target[:split_size]
y_test = news.target[split_size:]

from sklearn.cross_validation import cross_val_score, KFold
from scipy.stats import sem

def evaluate_cross_validation(clf, X, y, K):
    # create a k-fold croos validation iterator of k=5 folds
    cv = KFold(len(y), K, shuffle=True, random_state=0)
    # by default the score used is the one returned by score method of the estimator (accuracy)
    scores = cross_val_score(clf, X, y, cv=cv)
    print(scores)
    print(("Mean score: {0:.3f} (+/-{1:.3f})").format(
        np.mean(scores), sem(scores)))

from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.feature_extraction.text import TfidfVectorizer, HashingVectorizer, CountVectorizer

clf_1 = Pipeline([
    ('vect', CountVectorizer()),
    ('clf', MultinomialNB()),
])
clf_2 = Pipeline([
    ('vect', HashingVectorizer(non_negative=True)),
    ('clf', MultinomialNB()),
])
clf_3 = Pipeline([
    ('vect', TfidfVectorizer()),
    ('clf', MultinomialNB()),
])



clfs = [clf_1, clf_2, clf_3]
for clf in clfs:
    evaluate_cross_validation(clf, news.data, news.target, 5)


clf_4 = Pipeline([
    ('vect', TfidfVectorizer(
                token_pattern=r"\b[a-z0-9_\-\.]+[a-z][a-z0-9_\-\.]+\b")
    ),
    ('clf', MultinomialNB()),
])


evaluate_cross_validation(clf_4, news.data, news.target, 5)



stop_words = stopwords

clf_7 = Pipeline([
    ('vect', TfidfVectorizer(
                stop_words=stop_words,
                token_pattern=r"\b[a-z0-9_\-\.]+[a-z][a-z0-9_\-\.]+\b",         
    )),
    ('clf', MultinomialNB(alpha=0.01)),
])

from sklearn import metrics

def train_and_evaluate(clf, X_train, X_test, y_train, y_test):
    
    clf.fit(X_train, y_train)
    
    print("Accuracy on training set:")
    print(clf.score(X_train, y_train))
    print("Accuracy on testing set:")
    print(clf.score(X_test, y_test))
    
    y_pred = clf.predict(X_test)
    
    print("Classification Report:")
    print(metrics.classification_report(y_test, y_pred))
    print("Confusion Matrix:")
    print(metrics.confusion_matrix(y_test, y_pred))

train_and_evaluate(clf_7, X_train, X_test, y_train, y_test)


clf_7.named_steps['vect'].get_feature_names() 
