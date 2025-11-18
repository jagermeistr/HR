#!/usr/bin/env python3
import sys
import json
import warnings
warnings.filterwarnings("ignore")

import pandas as pd
import numpy as np
from statsmodels.tsa.statespace.sarimax import SARIMAX

def read_input():
    raw = sys.stdin.read()
    if not raw:
        return {}
    return json.loads(raw)

def main():
    data = read_input()
    dates = data.get('dates', [])
    values = data.get('values', [])
    forecast_days = int(data.get('forecast_days', 30))

    if not dates or not values:
        # return zeros if there's no data
        today = pd.to_datetime('today').normalize()
        out_dates = [(today + pd.Timedelta(days=i)).strftime('%Y-%m-%d') for i in range(1, forecast_days+1)]
        out_values = [0.0] * forecast_days
        print(json.dumps({'forecast_dates': out_dates, 'forecast_values': out_values}))
        return

    # Build series
    try:
        idx = pd.to_datetime(dates)
    except Exception:
        idx = pd.date_range(end=pd.Timestamp.today(), periods=len(values), freq='D')

    series = pd.Series(values, index=idx).asfreq('D').fillna(method='ffill').fillna(0.0)

    # Very small series fallback: use naive repeat
    if len(series) < 10:
        last = float(series.iloc[-1])
        out_dates = [(series.index[-1] + pd.Timedelta(days=i)).strftime('%Y-%m-%d') for i in range(1, forecast_days+1)]
        out_values = [last for _ in range(forecast_days)]
        print(json.dumps({'forecast_dates': out_dates, 'forecast_values': out_values}))
        return

    # Fit SARIMA: simple default parameters - you can tune these
    # seasonal_period=7 for weekly seasonality if production is weekly-patterned
    try:
        model = SARIMAX(series, order=(1,1,1), seasonal_order=(1,1,1,7), enforce_stationarity=False, enforce_invertibility=False)
        res = model.fit(disp=False, method='lbfgs', maxiter=200)
        forecast = res.get_forecast(steps=forecast_days)
        mean_forecast = forecast.predicted_mean
        out_values = [float(round(v, 2)) for v in mean_forecast.tolist()]
        out_dates = [(series.index[-1] + pd.Timedelta(days=i)).strftime('%Y-%m-%d') for i in range(1, forecast_days+1)]
    except Exception as e:
        # fallback if model fails
        last = float(series.iloc[-1])
        out_dates = [(series.index[-1] + pd.Timedelta(days=i)).strftime('%Y-%m-%d') for i in range(1, forecast_days+1)]
        out_values = [last for _ in range(forecast_days)]

    print(json.dumps({'forecast_dates': out_dates, 'forecast_values': out_values}))

if __name__ == '__main__':
    main()
