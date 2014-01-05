# coding=utf8
from nltk.util import ngrams
import sys
import codecs

def encodear(s): return codecs.encode(s,'utf8')
def decodear(s): return codecs.decode(s,'utf8')

sen = decodear(sys.argv[1])
n = int(sys.argv[2])

res = ""
sixgrams = ngrams(sen.split(), n)
for grams in sixgrams:
    res = res + " ## "
    for word in grams:
	    res = res + " " + encodear(word)

print(res)
