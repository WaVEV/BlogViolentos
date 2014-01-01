import nltk	
  

from nltk.corpus import cess_esp as cess
from nltk import UnigramTagger as ut
cess_sents = cess.tagged_sents()
uni_tag = ut(cess_sents)
sentence = raw_input("Enter	some text: ")  

print uni_tag.tag(sentence.split(" "))

print sorted(set(sentence))	





