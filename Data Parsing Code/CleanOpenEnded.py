#!/usr/bin/python

import numpy as np
from scipy.optimize import minimize
from scipy.stats import shapiro
import scipy.stats
import matplotlib.pyplot as plt
import sys
import itertools
import csv

'''
0: "Perimeter1.png":2.91393123655
1: "Line1.png":2.11605429697
2: "Area1.png": 4.27852
3: "Area2.png": 2.24069551
4: "Text1.png" (r's): 47
5: "CountProb-fig-6.png": 476
6: "CountProb-fig-7.png" (blue): 1043
7: "CountProb-fig-8.png": 568
8: "CountProb-fig-9.png": 2069
9: "CountProb-fig-10.png" (pink): 312
'''

sys.path.append("/home/posfaim/projects/turk/experiments/")
from TurkStuff import *

cans = [2.91393123655, 2.11605429697, 4.27852, 2.24069551, 47, 476, 347, 568, 2069, 312]


guesses = GetGuesses(openended_exps)
for gq in guesses:
	for g in gq:
		print "%f, "%g,
	print			

